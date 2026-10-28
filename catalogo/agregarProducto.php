<?php
// ============================================================
//  GALEZARZAL — Agregar producto al catálogo
// ============================================================
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/aut_verifica.php';
require_once __DIR__ . '/../includes/aut_visualizar_error.php';

requiereRol(['Vendedor','Administrador']);

$db = getDB();

// Verificar que el vendedor tiene local
$stLocal = $db->prepare(
    "SELECT l.idLocal FROM Local l
     JOIN Vendedor v ON v.idVendedor = l.idVendedor
     WHERE v.iduser = ? LIMIT 1"
);
$stLocal->execute([$_SESSION['iduser']]);
$idLocal = (int)$stLocal->fetchColumn();
if (!$idLocal) {
    header('Location: /galezarzal/local/crearLocal.php');
    exit;
}

$error      = '';
$categorias = $db->query("SELECT * FROM Categoria ORDER BY nomCategoria")->fetchAll();
$secciones  = ['Remates','Novedades','Ofertas','Destacados'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomProducto  = trim($_POST['nomProducto']  ?? '');
    $describProd  = trim($_POST['describProducto'] ?? '');
    $precio       = (float)str_replace(['.', ','], ['', '.'], $_POST['precio'] ?? 0);
    $talla        = trim($_POST['talla']        ?? '');
    $seccion      = trim($_POST['seccion']      ?? '');
    $idCategoria  = (int)($_POST['idcategoria'] ?? 0);
    $fotosRaw     = trim($_POST['fotosUrl']     ?? '');

    if (!$nomProducto || $precio <= 0 || !$idCategoria) {
        $error = 'Nombre, precio y categoría son obligatorios.';
    } else {
        $fotos    = array_filter(array_map('trim', explode(',', $fotosRaw)));
        $fotosJson = !empty($fotos) ? json_encode(array_values($fotos)) : null;

        $ins = $db->prepare(
            "INSERT INTO Producto (nomProducto, describProducto, precio, talla, seccion,
                                   fotosUrl, idcategoria, idLocal, estado)
             VALUES (?,?,?,?,?,?,?,?,'disponible')"
        );
        $ins->execute([$nomProducto, $describProd, $precio, $talla, $seccion,
                       $fotosJson, $idCategoria, $idLocal]);

        flash('exito', "Producto «{$nomProducto}» agregado correctamente.");
        header('Location: /galezarzal/local/miLocal.php');
        exit;
    }
}

$paginaActiva = 'milocal';
$tituloPagina = 'Agregar producto';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="hero" style="padding:1.5rem 2rem;">
  <h1 style="font-size:1.4rem;">Agregar nuevo producto</h1>
  <p style="color:#aaa;font-size:.85rem;margin-top:.2rem;">Completa la información del producto.</p>
</section>

<section class="seccion" style="max-width:680px;">
  <div class="form-wrap" style="max-width:100%;">
    <h2 class="form-titulo">Datos del producto</h2>
    <?php if ($error): ?>
      <div class="alerta alerta-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label>Nombre del producto *</label>
        <input type="text" name="nomProducto" placeholder="Ej: Vestido floral primavera"
               required value="<?= htmlspecialchars($_POST['nomProducto'] ?? '') ?>">
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Categoría *</label>
          <select name="idcategoria" required>
            <option value="">Selecciona…</option>
            <?php foreach ($categorias as $c): ?>
            <option value="<?= $c['idcategoria'] ?>"
              <?= (isset($_POST['idcategoria']) && $_POST['idcategoria'] == $c['idcategoria']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($c['nomCategoria']) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Precio (COP) *</label>
          <input type="number" name="precio" min="0" step="100" placeholder="Ej: $45.000"
                 required value="<?= htmlspecialchars($_POST['precio'] ?? '') ?>">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Tallas disponibles</label>
          <input type="text" name="talla" placeholder="S, M, L, XL..."
                 value="<?= htmlspecialchars($_POST['talla'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Sección</label>
          <select name="seccion">
            <option value="">Selecciona...</option>
            <?php foreach ($secciones as $s): ?>
            <option value="<?= $s ?>" <?= (($_POST['seccion'] ?? '') === $s) ? 'selected' : '' ?>><?= $s ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label>Descripción</label>
        <textarea name="describProducto" rows="3"
                  placeholder="Describe el producto, material, ocasión de uso..."><?= htmlspecialchars($_POST['describProducto'] ?? '') ?></textarea>
      </div>

      <div class="form-group">
        <label>Foto del producto <span style="font-weight:400;color:#aaa;">(URL — ej. Imgur, Google Photos)</span></label>
        <textarea name="fotosUrl" rows="2"
                  placeholder="https://i.imgur.com/foto1.jpg, https://i.imgur.com/foto2.jpg"><?= htmlspecialchars($_POST['fotosUrl'] ?? '') ?></textarea>
        <small style="color:var(--gris);font-size:.78rem;">Separa múltiples URLs con comas.</small>
      </div>

      <div class="form-acciones">
        <button type="submit" class="btn-primary">Guardar producto</button>
        <a href="/galezarzal/local/miLocal.php" class="btn-outline">Cancelar</a>
      </div>
    </form>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
