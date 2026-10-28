<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/aut_verifica.php';
require_once __DIR__ . '/../includes/aut_visualizar_error.php';
requiereRol(['Vendedor','Administrador']);

$db = getDB();

// Obtener local del vendedor
$stLocal = $db->prepare("SELECT l.idLocal FROM Local l JOIN Vendedor v ON v.idVendedor=l.idVendedor WHERE v.iduser=? LIMIT 1");
$stLocal->execute([$_SESSION['iduser']]);
$local = $stLocal->fetch();

$productos = [];
if ($local) {
    $stProd = $db->prepare("SELECT p.*,c.nomCategoria FROM Producto p JOIN Categoria c ON c.idcategoria=p.idcategoria WHERE p.idLocal=? ORDER BY p.nomProducto");
    $stProd->execute([$local['idLocal']]);
    $productos = $stProd->fetchAll();
}

// Acción editar estado rápido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambiarEstado'])) {
    $idProd = (int)$_POST['idProducto'];
    $estado = in_array($_POST['estado'],['disponible','agotado','inactivo']) ? $_POST['estado'] : 'disponible';
    $db->prepare("UPDATE Producto SET estado=? WHERE idProducto=? AND idLocal=?")
       ->execute([$estado, $idProd, $local['idLocal']]);
    header('Location: /galezarzal/configuracion/productos.php');
    exit;
}

$badgeMap = ['disponible'=>'activo','agotado'=>'agotado','inactivo'=>'pausado'];
$labelMap  = ['disponible'=>'ACTIVO','agotado'=>'AGOTADO','inactivo'=>'PAUSADO'];

$paginaActiva = 'configuracion'; $tituloPagina = 'Catálogo de productos'; $seccionActiva = 'productos';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="config-layout">
  <?php require_once __DIR__ . '/sidebar.php'; ?>
  <main class="config-main">
    <div class="config-panel">
      <div class="flex-between mb-2">
        <h2>Catálogo de productos</h2>
        <a href="/galezarzal/catalogo/agregarProducto.php" class="btn-primary btn-sm">+ Agregar producto</a>
      </div>

      <?php if (empty($productos)): ?>
        <div class="alerta alerta-info">No tienes productos aún. <a href="/galezarzal/catalogo/agregarProducto.php" style="color:var(--rojo);">Agregar primero</a></div>
      <?php else: ?>
      <div class="tabla-wrap">
        <table>
          <thead><tr><th>Producto</th><th>Precio</th><th>Tallas</th><th>Sección</th><th>Estado</th><th>Acciones</th></tr></thead>
          <tbody>
          <?php foreach ($productos as $p): ?>
          <tr>
            <td><?= htmlspecialchars($p['nomProducto']) ?></td>
            <td>$<?= number_format($p['precio'],0,',','.') ?></td>
            <td style="font-size:.82rem;color:var(--gris);"><?= htmlspecialchars($p['talla']??'—') ?></td>
            <td><span class="card-badge badge-mixto" style="font-size:.72rem;"><?= htmlspecialchars($p['seccion']??'—') ?></span></td>
            <td><span class="card-badge badge-<?= $badgeMap[$p['estado']] ?? 'mixto' ?>"><?= $labelMap[$p['estado']] ?? strtoupper($p['estado']) ?></span></td>
            <td>
              <a href="/galezarzal/catalogo/modificarProducto.php?id=<?= $p['idProducto'] ?>" class="btn-outline btn-sm">Editar</a>
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