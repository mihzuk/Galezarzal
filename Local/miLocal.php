<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/aut_verifica.php';
require_once __DIR__ . '/../includes/aut_visualizar_error.php';
requiereRol(['Vendedor','Administrador']);

$db = getDB();
$st = $db->prepare(
    "SELECT l.*, h.horarioApertura, h.horarioCierre, h.diasDisponibles
     FROM Local l
     JOIN Vendedor v ON v.idVendedor=l.idVendedor
     LEFT JOIN Horario h ON h.idHorario=l.idHorario
     WHERE v.iduser=? LIMIT 1"
);
$st->execute([$_SESSION['iduser']]);
$local = $st->fetch();

if (!$local) {
    header('Location: /galezarzal/local/crearLocal.php');
    exit;
}

$stProds = $db->prepare("SELECT p.*,c.nomCategoria FROM Producto p JOIN Categoria c ON c.idcategoria=p.idcategoria WHERE p.idLocal=? ORDER BY p.nomProducto");
$stProds->execute([$local['idLocal']]);
$productos = $stProds->fetchAll();

$activos   = count(array_filter($productos, fn($p) => $p['estado']==='disponible'));
$consultas = 3; // Placeholder

$badgeMap = ['disponible'=>'activo','agotado'=>'agotado','inactivo'=>'pausado'];
$labelMap  = ['disponible'=>'ACTIVO','agotado'=>'AGOTADO','inactivo'=>'PAUSADO'];

$exito = $_GET['exito'] ?? '';
$paginaActiva = 'milocal'; $tituloPagina = 'Mi Local';
require_once __DIR__ . '/../includes/header.php';
?>

<div style="background:var(--oscuro);color:var(--blanco);padding:1.5rem 2rem;">
  <h1 style="font-size:1.5rem;text-transform:uppercase;"><?= htmlspecialchars($local['nomLocal']) ?></h1>
  <p style="color:#aaa;font-size:.88rem;margin-top:.2rem;">Visualiza tu catálogo y consultas desde un solo lugar</p>
</div>

<section class="seccion">
  <?php if ($exito): ?><div class="alerta alerta-exito">¡Local registrado! Está en revisión por el administrador.</div><?php endif; ?>

  <!-- Stats -->
  <div class="stats-grid">
    <div class="stat-card"><div class="num"><?= $activos ?></div><div class="lbl">Productos activos</div></div>
    <div class="stat-card"><div class="num">—</div><div class="lbl">Visitas hoy</div></div>
    <div class="stat-card"><div class="num"><?= $consultas ?></div><div class="lbl">Consultas sin resp.</div></div>
  </div>

  <!-- Acciones rápidas -->
  <div style="margin-bottom:1.5rem;">
    <strong style="font-size:.95rem;">Acciones rápidas</strong>
    <div style="display:flex;gap:.75rem;flex-wrap:wrap;margin-top:.6rem;">
      <a href="/galezarzal/catalogo/agregarProducto.php" class="btn-primary">+ Agregar producto</a>
      <a href="/galezarzal/configuracion/infoLocal.php"  class="btn-outline">Editar información</a>
      <a href="/galezarzal/local/verLocal.php?id=<?= $local['idLocal'] ?>" class="btn-outline">Ver mi local público</a>
    </div>
  </div>

  <!-- Catálogo -->
  <h2 style="font-size:1rem;font-weight:700;margin-bottom:.75rem;">Tu catálogo</h2>
  <?php if (empty($productos)): ?>
  <p class="text-gris">Aún no tienes productos. <a href="/galezarzal/catalogo/agregarProducto.php" style="color:var(--rojo);">Agrega el primero</a></p>
  <?php else: ?>
  <div class="tabla-wrap">
    <table>
      <thead><tr><th>Producto</th><th>Precio</th><th>Tallas</th><th>Sección</th><th>Acciones</th><th>Estado</th></tr></thead>
      <tbody>
      <?php foreach ($productos as $p): ?>
      <tr>
        <td><?= htmlspecialchars($p['nomProducto']) ?></td>
        <td>$<?= number_format($p['precio'],0,',','.') ?></td>
        <td style="font-size:.82rem;color:var(--gris);"><?= htmlspecialchars($p['talla']??'—') ?></td>
        <td><span class="card-badge badge-mixto" style="font-size:.7rem;"><?= htmlspecialchars($p['seccion']??'—') ?></span></td>
        <td><a href="/galezarzal/catalogo/modificarProducto.php?id=<?= $p['idProducto'] ?>" class="btn-outline btn-sm" style="color:var(--rojo);border-color:var(--rojo);">Editar</a></td>
        <td><span class="card-badge badge-<?= $badgeMap[$p['estado']]??'mixto' ?>"><?= $labelMap[$p['estado']]??strtoupper($p['estado']) ?></span></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>