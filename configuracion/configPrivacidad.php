<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/aut_verifica.php';
requiereLogin();

$exito = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $exito = 'Configuración de privacidad guardada.';
}

$paginaActiva = 'configuracion'; $tituloPagina = 'Privacidad'; $seccionActiva = 'privacidad';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="config-layout">
  <?php require_once __DIR__ . '/sidebar.php'; ?>
  <main class="config-main">
    <div class="config-panel">
      <h2>Privacidad</h2>
      <?php if ($exito): ?><div class="alerta alerta-exito"><?= htmlspecialchars($exito) ?></div><?php endif; ?>
      <form method="POST">
        <?php foreach ([
          ['Mostrar nombre completo',     'En reseñas y contactos con vendedores'],
          ['Permitir reseñas visibles',   'Tus reseñas serán públicas'],
          ['Visible para vendedores',     'Que puedan ver tu perfil al contactarlos'],
        ] as [$tit, $desc]): ?>
        <div class="toggle-row">
          <div class="toggle-info"><h4><?= $tit ?></h4><p><?= $desc ?></p></div>
          <label class="toggle">
            <input type="checkbox" name="priv[]" value="<?= md5($tit) ?>" checked>
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