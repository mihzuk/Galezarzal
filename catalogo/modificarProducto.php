<?php
// ============================================================
//  GALEZARZAL — Editar / eliminar producto
// ============================================================
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/aut_verifica.php';
require_once __DIR__ . '/../includes/aut_visualizar_error.php';

requiereRol(['Vendedor','Administrador']);

$idProducto = (int)($_GET['id'] ?? 0);
if (!$idProducto) mostrarError('Error', 'Producto no especificado.');

$db = getDB();

// Verificar que el producto pertenece al local del vendedor
$st = $db->prepare(
    "SELECT p.*, c.nomCategoria
     FROM Producto p
     JOIN Categoria c ON c.idcategoria = p.idcategoria
     JOIN Local l ON l.idLocal = p.idLocal
     JOIN Vendedor v ON v.idVendedor = l.idVendedor
     WHERE p.idProducto = ? AND (v.iduser = ? OR ? = 'Administrador')"
);
$st->execute([$idProducto, $_SESSION['iduser'], $_SESSION['nomRol'] ?? '']);
$producto = $st->fetch();
if (!$producto) mostrarError('Sin permiso', 'No puedes editar este producto.');

$categorias = $db->query("SELECT * FROM Categoria ORDER BY nomCategoria")->fetchAll();
$secciones  = ['Remates','Novedades','Ofertas','Destacados'];
$estados    = ['disponible' => 'Disponible', 'agotado' => 'Agotado', 'inactivo' => 'Inactivo (oculto)'];
$error = '';
$exito = '';

// Eliminar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar'])) {
    $db->prepare("UPDATE Producto SET estado='inactivo' WHERE idProducto=?")->execute([$idProducto]);
    flash('exito', 'Producto eliminado.');
    header('Location: /galezarzal/local/miLocal.php');
    exit;
}

// Guardar cambios
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['eliminar'])) {
    $nomProducto = trim($_POST['nomProducto']      ?? '');
    $describProd = trim($_POST['describProducto']  ?? '');
    $precio      = (float)($_POST['precio']        ?? 0);
    $talla       = trim($_POST['talla']            ?? '');
    $seccion     = trim($_POST['seccion']          ?? '');
    $idCategoria = (int)($_POST['idcategoria']     ?? 0);
    $estado      = in_array($_POST['estado'] ?? '', array_keys($estados)) ? $_POST['estado'] : 'disponible';
    $fotosRaw    = trim($_POST['fotosUrl']         ?? '');

    if (!$nomProducto || $precio <= 0 || !$idCategoria) {
        $error = 'Nombre, precio y categoría son obligatorios.';
    } else {
        $fotos     = array_filter(array_map('trim', explode(',', $fotosRaw)));
        $fotosJson = !empty($fotos) ? json_encode(array_values($fotos)) : null;

        $db->prepare(
            "UPDATE Producto SET nomProducto=?, describProducto=?, precio=?, talla=?,
                                 seccion=?, fotosUrl=?, idcategoria=?, estado=?
             WHERE idProducto=?"
        )->execute([$nomProducto, $describProd, $precio, $talla,
                    $seccion, $fotosJson, $idCategoria, $estado, $idProducto]);

        $exito = 'Producto actualizado correctamente.';
        $st->execute([$idProducto, $_SESSION['iduser'], $_SESSION['nomRol'] ?? '']);
        $producto = $st->fetch();
    }
}

$fotosActuales = implode(', ', json_decode($producto['fotosUrl'] ?? '[]', true) ?: []);

$paginaActiva = 'milocal';
$tituloPagina = 'Editar producto';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="hero" style="padding:1.5rem 2rem;">
  <h1 style="font-size:1.4rem;">Editar producto</h1>
</section>

<section class="seccion" style="max-width:680px;">
  <div class="form-wrap" style="max-width:100%;">
    <?php if ($error): ?><div class="alerta alerta-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($exito): ?><div class="alerta alerta-exito"><?= htmlspecialchars($exito) ?></div><?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label>Nombre del producto *</label>
        <input type="text" name="nomProducto" required
               value="<?= htmlspecialchars($producto['nomProducto']) ?>">
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Categoría *</label>
          <select name="idcategoria" required>
            <?php foreach ($categorias as $c): ?>
            <option value="<?= $c['idcategoria'] ?>"
              <?= $producto['idcategoria'] == $c['idcategoria'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($c['nomCategoria']) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Sección</label>
          <select name="seccion">
            <option value="">Selecciona…</option>
            <?php foreach ($secciones as $s): ?>
            <option value="<?= $s ?>" <?= $producto['seccion'] === $s ? 'selected' : '' ?>><?= $s ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label>Descripción</label>
        <textarea name="describProducto" rows="3"><?= htmlspecialchars($producto['describProducto'] ?? '') ?></textarea>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Precio (COP) *</label>
          <input type="number" name="precio" min="0" step="100"
                 required value="<?= $producto['precio'] ?>">
        </div>
        <div class="form-group">
          <label>Tallas</label>
          <input type="text" name="talla" placeholder="S, M, L" value="<?= htmlspecialchars($producto['talla'] ?? '') ?>">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Estado</label>
          <select name="estado">
            <?php foreach ($estados as $k => $v): ?>
            <option value="<?= $k ?>" <?= $producto['estado'] === $k ? 'selected' : '' ?>><?= $v ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>URLs de fotos <span style="font-weight:400;color:#aaa;">(separadas por coma)</span></label>
          <input type="text" name="fotosUrl" value="<?= htmlspecialchars($fotosActuales) ?>"
                 placeholder="https://i.imgur.com/foto1.jpg">
        </div>
      </div>
      <div class="form-acciones">
        <button type="submit" class="btn-primary">Guardar cambios</button>
        <a href="/galezarzal/local/miLocal.php" class="btn-outline">Cancelar</a>
      </div>
    </form>

    <div style="margin-top:2rem;padding-top:1rem;border-top:1px solid #eee;">
      <p style="font-size:.85rem;color:var(--gris);margin-bottom:.75rem;">Zona de peligro</p>
      <form method="POST" onsubmit="return confirm('¿Seguro que quieres eliminar este producto?');">
        <input type="hidden" name="eliminar" value="1">
        <button type="submit" class="btn-peligro">Eliminar producto</button>
      </form>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
