<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/aut_verifica.php';
requiereLogin();

$exito = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // En una BD real se guardarían preferencias en tabla separada
    $exito = 'Preferencias de notificaciones guardadas.';
}

$paginaActiva = 'configuracion'; $tituloPagina = 'Notificaciones'; $seccionActiva = 'notificaciones';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="config-layout">
  <?php require_once __DIR__ . '/sidebar.php'; ?>
  <main class="config-main">
    <div class="config-panel">
      <h2>Notificaciones</h2>
      <?php if ($exito): ?><div class="alerta alerta-exito"><?= htmlspecialchars($exito) ?></div><?php endif; ?>
      <form method="POST">
        <?php foreach ([
          ['Nuevos productos en locales favoritos', 'De locales que sigues'],
          ['Respuestas de vendedores', 'Cuando un local te responde'],
          ['Novedades de la plataforma', 'Nuevos locales afiliados'],
          ['Por correo electrónico', 'Recibir notificaciones también por email'],
        ] as [$tit, $desc]): ?>
        <div class="toggle-row">
          <div class="toggle-info">
            <h4><?= $tit ?></h4>
            <p><?= $desc ?></p>
          </div>
          <label class="toggle">
            <input type="checkbox" name="notif[]" value="<?= md5($tit) ?>" checked>
            <span class="slider"></span>
          </label>
        </div>
        <?php endforeach; ?>
        <button type="submit" class="btn-primary" style="margin-top:1.5rem;">Guardar</button>
      </form>
    </div>
  </main>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>