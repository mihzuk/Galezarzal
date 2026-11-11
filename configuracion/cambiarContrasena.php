<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/aut_verifica.php';
require_once __DIR__ . '/../includes/aut_visualizar_error.php';
requiereLogin();

$error = ''; $exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $actual  = $_POST['actual']  ?? '';
    $nueva   = $_POST['nueva']   ?? '';
    $confirmar = $_POST['confirmar'] ?? '';
    $db = getDB();
    $row = $db->prepare("SELECT passwordHash FROM Usuario WHERE iduser=?");
    $row->execute([$_SESSION['iduser']]);
    $hash = $row->fetchColumn();

    if (!password_verify($actual, $hash)) {
        $error = 'La contraseña actual es incorrecta.';
    } elseif (strlen($nueva) < 6) {
        $error = 'La nueva contraseña debe tener al menos 6 caracteres.';
    } elseif ($nueva !== $confirmar) {
        $error = 'Las contraseñas nuevas no coinciden.';
    } else {
        $db->prepare("UPDATE Usuario SET passwordHash=? WHERE iduser=?")
           ->execute([password_hash($nueva, PASSWORD_DEFAULT), $_SESSION['iduser']]);
        $exito = 'Contraseña actualizada correctamente.';
    }
}

$paginaActiva = 'configuracion'; $tituloPagina = 'Seguridad'; $seccionActiva = 'seguridad';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="config-layout">
  <?php require_once __DIR__ . '/sidebar.php'; ?>
  <main class="config-main">
    <div class="config-panel">
      <h2>Seguridad</h2>
      <?php if ($error): ?><div class="alerta alerta-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
      <?php if ($exito): ?><div class="alerta alerta-exito"><?= htmlspecialchars($exito) ?></div><?php endif; ?>
      <form method="POST">
        <div class="form-group"><label>Contraseña actual</label><input type="password" name="actual" placeholder="········" required></div>
        <div class="form-group"><label>Nueva contraseña</label><input type="password" name="nueva" placeholder="········" required></div>
        <div class="form-group"><label>Confirmar contraseña</label><input type="password" name="confirmar" placeholder="········" required></div>
        <button type="submit" class="btn-primary">Cambiar contraseña</button>
      </form>
    </div>
  </main>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>