<?php
// ============================================================
//  GALEZARZAL — Admin: Moderación de productos
// ============================================================
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/aut_verifica.php';

requiereRol(['Administrador']);

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idProducto'], $_POST['accion'])) {
    $id = (int)$_POST['idProducto'];
    $estado = match($_POST['accion']) {
        'activar'   => 'disponible',
        'agotado'   => 'agotado',
        'eliminar'  => 'inactivo',
        default     => null,
    };
    if ($estado) $db->prepare("UPDATE Producto SET estado=? WHERE idProducto=?")->execute([$estado, $id]);
    header('Location: /galezarzal/admin/moderarProductos.php');
    exit;
}

$q   = trim($_GET['q'] ?? '');
$est = $_GET['estado'] ?? '';
$where = ["1=1"];
$params = [];
if ($q) { $where[] = "(p.nomProducto LIKE ? OR l.nomLocal LIKE ?)"; $params[] = "%$q%"; $params[] = "%$q%"; }
if (in_array($est, ['disponible','agotado','inactivo'])) { $where[] = "p.estado = ?"; $params[] = $est; }

$st = $db->prepare(
    "SELECT p.*, c.nomCategoria, l.nomLocal FROM Producto p
     JOIN Categoria c ON c.idcategoria = p.idcategoria
     JOIN Local l ON l.idLocal = p.idLocal
     WHERE " . implode(' AND ', $where) . "
     ORDER BY p.idProducto DESC LIMIT 100"
);
$st->execute($params);
$productos = $st->fetchAll();
$badgeMap  = ['disponible'=>'activo','agotado'=>'agotado','inactivo'=>'pausado'];

$paginaActiva  = 'configuracion';
$tituloPagina  = 'Moderación de productos';
$seccionActiva = 'admin-moderacion';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="config-layout">
  <?php require_once __DIR__ . '/../configuracion/sidebar.php'; ?>
  <main class="config-main">
    <div class="config-panel">
      <h2>Moderación de productos</h2>
      <form method="GET" style="display:flex;gap:.75rem;margin-bottom:1.25rem;flex-wrap:wrap;">
        <div class="buscador" style="max-width:300px;">
          <input type="text" name="q" placeholder="Producto o local…" value="<?= htmlspecialchars($q) ?>">
          <button type="submit">🔍</button>
        </div>
        <select name="estado" onchange="this.form.submit()" style="border:1.5px solid #ddd;border-radius:var(--radio);padding:.5rem .75rem;background:var(--crema);">
          <option value="">Todos los estados</option>
          <option value="disponible" <?= $est==='disponible'?'selected':'' ?>>Disponibles</option>
          <option value="agotado"    <?= $est==='agotado'   ?'selected':'' ?>>Agotados</option>
          <option value="inactivo"   <?= $est==='inactivo'  ?'selected':'' ?>>Inactivos</option>
        </select>
      </form>

      <?php if (empty($productos)): ?>
        <div class="alerta alerta-info">No hay productos con esos criterios.</div>
      <?php else: ?>
      <div class="tabla-wrap">
        <table>
          <thead><tr><th>Producto</th><th>Local</th><th>Precio</th><th>Estado</th><th>Acciones</th></tr></thead>
          <tbody>
          <?php foreach ($productos as $p): ?>
          <tr>
            <td><?= htmlspecialchars($p['nomProducto']) ?><br>
                <small style="color:var(--gris);font-size:.75rem;"><?= htmlspecialchars($p['nomCategoria']) ?></small></td>
            <td style="font-size:.85rem;"><?= htmlspecialchars($p['nomLocal']) ?></td>
            <td style="font-weight:700;color:var(--rojo);">$<?= number_format($p['precio'],0,',','.') ?></td>
            <td><span class="card-badge badge-<?= $badgeMap[$p['estado']]??'mixto' ?>"><?= strtoupper($p['estado']) ?></span></td>
            <td>
              <form method="POST" style="display:flex;gap:.4rem;flex-wrap:wrap;">
                <input type="hidden" name="idProducto" value="<?= $p['idProducto'] ?>">
                <?php if ($p['estado'] !== 'disponible'): ?>
                  <button name="accion" value="activar" class="btn-outline btn-sm">Activar</button>
                <?php endif; ?>
                <?php if ($p['estado'] !== 'inactivo'): ?>
                  <button name="accion" value="eliminar" class="btn-gris btn-sm"
                          onclick="return confirm('¿Ocultar este producto?')">Ocultar</button>
                <?php endif; ?>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
