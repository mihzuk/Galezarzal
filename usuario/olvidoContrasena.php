<?php
// ============================================================
//  GALEZARZAL — Olvidé mi contraseña
// ============================================================
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/aut_visualizar_error.php';

$mensaje = '';
$tipo    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje = 'Ingresa un correo válido.';
        $tipo    = 'error';
    } else {
        $db = getDB();
        $st = $db->prepare("SELECT iduser FROM Usuario WHERE email=? AND estado='activo'");
        $st->execute([$email]);
        if ($st->fetchColumn()) {
            // En producción aquí se enviaría el correo con token.
            $mensaje = 'Si el correo está registrado, recibirás instrucciones en tu bandeja de entrada.';
            $tipo    = 'exito';
        } else {
            $mensaje = 'Si el correo está registrado, recibirás instrucciones en tu bandeja de entrada.';
            $tipo    = 'exito';
        }
    }
}

$paginaActiva = '';
$tituloPagina = 'Restablecer contraseña';
require_once __DIR__ . '/../includes/header.php';
?>
<section style="min-height:60vh;display:flex;align-items:center;justify-content:center;padding:2rem;">
  <div class="form-wrap">
    <h2 class="form-titulo">Restablecer contraseña</h2>
    <p style="font-size:.88rem;color:var(--gris);margin-bottom:1.25rem;">
      Ingresa tu correo electrónico y te enviaremos instrucciones para restablecer tu contraseña.
    </p>
    <?php if ($mensaje): ?>
      <div class="alerta alerta-<?= $tipo ?>"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>
    <form method="POST">
      <div class="form-group">
        <label>Correo electrónico</label>
        <input type="email" name="email" placeholder="tucorreo@correo.com" required>
      </div>
      <div class="form-acciones">
        <button type="submit" class="btn-primary">Enviar instrucciones</button>
        <a href="/galezarzal/usuario/ingresarCuenta.php" class="btn-outline">Volver al inicio de sesión</a>
      </div>
    </form>
  </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>