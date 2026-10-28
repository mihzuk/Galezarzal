<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/aut_verifica.php';
require_once __DIR__ . '/../includes/aut_visualizar_error.php';
requiereRol(['Vendedor','Administrador']);

$db = getDB();
$exito = '';

$st = $db->prepare("SELECT l.* FROM Local l JOIN Vendedor v ON v.idVendedor=l.idVendedor WHERE v.iduser=? LIMIT 1");
$st->execute([$_SESSION['iduser']]);
$local = $st->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $local) {
    $estado = isset($_POST['visible']) ? 'activo' : 'inactivo';
    $db->prepare("UPDATE Local SET estado=? WHERE idLocal=?")->execute([$estado, $local['idLocal']]);
    $exito = 'Visibilidad actualizada.';
    $local['estado'] = $estado;
}

$paginaActiva = 'configuracion'; $tituloPagina = 'Visibilidad'; $seccionActiva = 'visibilidad';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="config-layout">
  <?php require_once __DIR__ . '/sidebar.php'; ?>
  <main class="config-main">
    <div class="config-panel">
      <h2>Visibilidad del local</h2>
      <?php if ($exito): ?><div class="alerta alerta-exito"><?= htmlspecialchars($exito) ?></div><?php endif; ?>
      <?php if (!$local): ?><div class="alerta alerta-info">No tienes un local registrado.</div><?php else: ?>
      <form method="POST">
        <?php foreach ([
          ['visible',  'Estado del local',          'Visible públicamente para los compradores'],
          ['contacto', 'Aceptar contacto',           'Los compradores pueden enviarte mensajes'],
          ['recom',    'Aparecer en recomendados',   'Mostrar tu local en el Index principal'],
          ['prodvis',  'Productos activos visibles',  'Los productos activos son públicos'],
        ] as [$key,$tit,$desc]): ?>
        <div class="toggle-row">
          <div class="toggle-info"><h4><?= $tit ?></h4><p><?= $desc ?></p></div>
          <label class="toggle">
            <input type="checkbox" name="<?= $key ?>" <?= ($local['estado']==='activo' && $key==='visible') ? 'checked' : ($key!=='visible'?'checked':'') ?>>
            <span class="slider"></span>
          </label>
        </div>
        <?php endforeach; ?>
        <div class="form-acciones" style="margin-top:1.25rem;">
          <button type="submit" class="btn-primary">Guardar</button>
          <a href="/galezarzal/local/verLocal.php?id=<?= $local['idLocal'] ?>" class="btn-outline">Cancelar</a>
        </div>
      </form>
      <?php endif; ?>
    </div>
  </main>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>