<?php
// ============================================================
//  GALEZARZAL — Olvidé mi contraseña
//  Genera un enlace de un solo uso (vence en 60 minutos).
//  En desarrollo local el enlace se muestra en pantalla y se
//  guarda en logs/recuperacion.log (ver includes/config.php).
// ============================================================
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/aut_visualizar_error.php';
require_once __DIR__ . '/../includes/recuperacion.php';

$mensaje = '';
$tipo    = '';
$enlace  = ''; // solo se llena en desarrollo

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
        $mensaje = 'Ingresa un correo válido.';
        $tipo    = 'error';
    } else {
        try {
            $db = getDB();
            $st = $db->prepare("SELECT iduser FROM usuario WHERE email=? AND estado='activo' LIMIT 1");
            $st->execute([$email]);
            $iduser = $st->fetchColumn();

            if ($iduser) {
                $token = crearTokenRecuperacion($db, (int)$iduser);
                $url   = enlaceRecuperacion($token);
                entregarEnlaceRecuperacion($email, $url);
                if (enlaceEnPantalla()) $enlace = $url;
            }
            // Mismo mensaje exista o no el correo, para no revelar quién tiene cuenta
            $mensaje = 'Si el correo está registrado, recibirás instrucciones en tu bandeja de entrada.';
            $tipo    = 'exito';
        } catch (PDOException $e) {
            registrarError('Recuperación de contraseña: ' . $e->getMessage());
            $mensaje = 'No pudimos procesar tu solicitud. Inténtalo de nuevo en unos minutos.';
            $tipo    = 'error';
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
    <?php if ($enlace): ?>
      <div class="alerta alerta-info" style="word-break:break-all;">
        <strong>Modo desarrollo:</strong> no se envía correo. Usa este enlace
        (vence en <?= TOKEN_MINUTOS ?> minutos y solo sirve una vez):<br>
        <a href="<?= htmlspecialchars($enlace) ?>" id="enlace-recuperacion"><?= htmlspecialchars($enlace) ?></a>
      </div>
    <?php endif; ?>
    <form method="POST">
      <div class="form-group">
        <label>Correo electrónico</label>
        <input type="email" name="email" placeholder="tucorreo@correo.com" maxlength="100" required>
      </div>
      <div class="form-acciones">
        <button type="submit" class="btn-primary">Enviar instrucciones</button>
        <a href="/galezarzal/usuario/ingresarCuenta.php" class="btn-outline">Volver al inicio de sesión</a>
      </div>
    </form>
  </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
