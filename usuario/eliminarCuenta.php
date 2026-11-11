<?php
// ============================================================
//  GALEZARZAL — Eliminar cuenta
// ============================================================
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/aut_verifica.php';
require_once __DIR__ . '/../includes/aut_visualizar_error.php';

requiereLogin();

$error  = '';
$exito  = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $db = getDB();
    $st = $db->prepare("SELECT passwordHash FROM Usuario WHERE iduser=?");
    $st->execute([$_SESSION['iduser']]);
    $row = $st->fetch();
    if ($row && password_verify($password, $row['passwordHash'])) {
        $db->prepare("UPDATE Usuario SET estado='inactivo' WHERE iduser=?")
           ->execute([$_SESSION['iduser']]);
        session_destroy();
        header('Location: /galezarzal/index.php?msg=cuenta_eliminada');
        exit;
    } else {
        $error = 'Contraseña incorrecta. No se pudo eliminar la cuenta.';
    }
}

$paginaActiva = 'configuracion';
$tituloPagina = 'Eliminar cuenta';
require_once __DIR__ . '/../includes/header.php';
?>
<section style="min-height:60vh;display:flex;align-items:center;justify-content:center;padding:2rem;">
  <div class="form-wrap">
    <h2 class="form-titulo" style="color:#c0392b;">Eliminar cuenta</h2>
    <p class="alerta alerta-error" style="margin-bottom:1rem;">
      Esta acción es permanente y desactivará tu cuenta. Tus datos no serán borrados pero no podrás acceder.
    </p>
    <?php if ($error): ?>
      <div class="alerta alerta-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
      <div class="form-group">
        <label>Confirma tu contraseña</label>
        <input type="password" name="password" placeholder="········" required>
      </div>
      <div class="form-acciones">
        <button type="submit" class="btn-peligro">Eliminar mi cuenta</button>
        <a href="/galezarzal/configuracion/configuracion.php" class="btn-outline">Cancelar</a>
      </div>
    </form>
  </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>