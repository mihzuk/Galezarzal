<?php
// ============================================================
//  GALEZARZAL — Restablecer contraseña con el enlace recibido
//  El token es de un solo uso y vence (ver includes/recuperacion.php).
//  La contraseña nueva se guarda con password_hash.
// ============================================================
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/aut_visualizar_error.php';
require_once __DIR__ . '/../includes/validaciones.php';
require_once __DIR__ . '/../includes/recuperacion.php';

// El token viaja en la URL: que no se filtre por el Referer ni se guarde en caché
header('Referrer-Policy: no-referrer');
header('Cache-Control: no-store');

$token = $_POST['token'] ?? $_GET['token'] ?? '';
$token = is_string($token) ? trim($token) : '';

$error = '';
$db    = getDB();
$info  = consultarToken($db, $token);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $info['estado'] === 'valido') {
    $pass      = $_POST['password']        ?? ''; // sin trim: puede llevar espacios
    $confirmar = $_POST['confirmPassword'] ?? '';
    $error     = validarPassword($pass, $confirmar);

    if ($error === '') {
        try {
            $db->beginTransaction();
            // Gasta el token solo si sigue libre y vigente: si dos personas lo
            // envían a la vez, solo una lo consigue
            $gasta = $db->prepare(
                "UPDATE tokenrecuperacion SET fechaUso = NOW()
                 WHERE idToken = ? AND fechaUso IS NULL AND fechaExpira > NOW()"
            );
            $gasta->execute([$info['idToken']]);

            if ($gasta->rowCount() !== 1) {
                $db->rollBack();
                $info = consultarToken($db, $token); // ahora dirá 'usado' o 'vencido'
            } else {
                $db->prepare("UPDATE usuario SET passwordHash = ? WHERE iduser = ?")
                   ->execute([password_hash($pass, PASSWORD_DEFAULT), $info['iduser']]);
                // Cualquier otro enlace pendiente de este usuario deja de servir
                $db->prepare(
                    "UPDATE tokenrecuperacion SET fechaExpira = NOW()
                     WHERE iduser = ? AND fechaUso IS NULL AND fechaExpira > NOW()"
                )->execute([$info['iduser']]);
                $db->commit();

                flash('exito', 'Tu contraseña se cambió. Ya puedes iniciar sesión con la nueva.');
                header('Location: /galezarzal/usuario/ingresarCuenta.php');
                exit;
            }
        } catch (PDOException $e) {
            if ($db->inTransaction()) $db->rollBack();
            registrarError('Restablecer contraseña: ' . $e->getMessage());
            $error = 'No pudimos cambiar tu contraseña. Inténtalo de nuevo en unos minutos.';
        }
    }
}

$paginaActiva = '';
$tituloPagina = 'Nueva contraseña';
require_once __DIR__ . '/../includes/header.php';
?>
<section style="min-height:60vh;display:flex;align-items:center;justify-content:center;padding:2rem;">
  <div class="form-wrap">
    <h2 class="form-titulo">Nueva contraseña</h2>

    <?php if ($info['estado'] !== 'valido'): ?>
      <div class="alerta alerta-error"><?= htmlspecialchars(mensajeToken($info['estado'])) ?></div>
      <div class="form-acciones">
        <a href="/galezarzal/usuario/olvidoContrasena.php" class="btn-primary">Pedir un enlace nuevo</a>
        <a href="/galezarzal/usuario/ingresarCuenta.php" class="btn-outline">Iniciar sesión</a>
      </div>
    <?php else: ?>
      <p style="font-size:.88rem;color:var(--gris);margin-bottom:1.25rem;">
        Escribe la contraseña nueva para tu cuenta (entre 6 y 72 caracteres).
      </p>
      <?php if ($error): ?>
        <div class="alerta alerta-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <form method="POST">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
        <div class="form-group">
          <label>Contraseña nueva</label>
          <input type="password" name="password" placeholder="········" required minlength="6" maxlength="72" autocomplete="new-password">
        </div>
        <div class="form-group">
          <label>Confirmar contraseña nueva</label>
          <input type="password" name="confirmPassword" placeholder="········" required minlength="6" maxlength="72" autocomplete="new-password">
        </div>
        <div class="form-acciones">
          <button type="submit" class="btn-primary">Cambiar contraseña</button>
        </div>
      </form>
    <?php endif; ?>
  </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
