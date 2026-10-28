<?php
// ============================================================
//  GALEZARZAL — Iniciar sesión / Crear cuenta
// ============================================================
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/aut_verifica.php';
require_once __DIR__ . '/../includes/aut_visualizar_error.php';

if (sesionActiva()) {
    header('Location: /galezarzal/index.php');
    exit;
}

$error   = '';
$exito   = '';
$tab     = $_GET['tab'] ?? 'login'; // 'login' | 'registro'

// ── POST: Iniciar sesión ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'login') {
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = 'Completa todos los campos.';
    } else {
        $db = getDB();
        $st = $db->prepare(
            "SELECT u.*, r.nomRol
             FROM Usuario u
             JOIN Rol r ON r.idRol = u.idRol
             WHERE u.email = ? AND u.estado = 'activo'
             LIMIT 1"
        );
        $st->execute([$email]);
        $usuario = $st->fetch();

        if ($usuario && password_verify($password, $usuario['passwordHash'])) {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['iduser']   = $usuario['iduser'];
            $_SESSION['username'] = $usuario['username'];
            $_SESSION['email']    = $usuario['email'];
            $_SESSION['nomRol']   = $usuario['nomRol'];
            $_SESSION['nombre']   = $usuario['primerNombre'];
            header('Location: /galezarzal/index.php');
            exit;
        } else {
            $error = 'Correo o contraseña incorrectos.';
        }
    }
    $tab = 'login';
}

// ── POST: Crear cuenta ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'registro') {
    $primerNombre    = trim($_POST['primerNombre']    ?? '');
    $primerApellido  = trim($_POST['primerApellido']  ?? '');
    $username        = trim($_POST['username']        ?? '');
    $emailReg        = trim($_POST['email_reg']       ?? '');
    $celular         = trim($_POST['celular']         ?? '');
    $password        = $_POST['password_reg']         ?? '';
    $confirmPassword = $_POST['confirmPassword']      ?? '';

    if (!$primerNombre || !$primerApellido || !$username || !$emailReg || !$password) {
        $error = 'Completa todos los campos obligatorios.';
    } elseif (!filter_var($emailReg, FILTER_VALIDATE_EMAIL)) {
        $error = 'El correo no tiene formato válido.';
    } elseif (strlen($password) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Las contraseñas no coinciden.';
    } else {
        $db = getDB();
        $st = $db->prepare("SELECT COUNT(*) FROM Usuario WHERE email=? OR username=?");
        $st->execute([$emailReg, $username]);
        if ($st->fetchColumn() > 0) {
            $error = 'El correo o nombre de usuario ya está registrado.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins  = $db->prepare(
                "INSERT INTO Usuario (username, email, passwordHash, primerNombre, primerApellido, celular, idTipDocu, idRol)
                 VALUES (?,?,?,?,?,?,1,1)"
            );
            $ins->execute([$username, $emailReg, $hash, $primerNombre, $primerApellido, $celular]);
            $exito = '¡Cuenta creada con éxito! Ahora puedes iniciar sesión.';
            $tab   = 'login';
        }
    }
    if ($error) $tab = 'registro';
}

$paginaActiva = '';
$tituloPagina = 'Iniciar sesión';
require_once __DIR__ . '/../includes/header.php';
?>

<section style="min-height:70vh;display:flex;align-items:center;justify-content:center;padding:2rem;">
  <div style="width:100%;max-width:480px;">

    <!-- Hero auth -->
    <div style="background:var(--oscuro);color:var(--blanco);border-radius:var(--radio) var(--radio) 0 0;padding:1.75rem 2rem;text-align:center;">
      <h1 style="font-size:1.5rem;">Bienvenido a <span style="color:var(--rojo);">Galezarzal</span></h1>
      <p style="color:#aaa;font-size:.88rem;margin-top:.3rem;">La vitrina digital de la moda local de Zarzal</p>
    </div>

    <!-- Tabs -->
    <div style="display:flex;background:var(--crema);border-bottom:2px solid #ddd;">
      <a href="?tab=login"
         style="flex:1;text-align:center;padding:.75rem;font-weight:600;font-size:.95rem;
                <?= $tab==='login' ? 'background:var(--blanco);border-bottom:3px solid var(--rojo);color:var(--rojo);' : 'color:#888;' ?>">
        Iniciar sesión
      </a>
      <a href="?tab=registro"
         style="flex:1;text-align:center;padding:.75rem;font-weight:600;font-size:.95rem;
                <?= $tab==='registro' ? 'background:var(--blanco);border-bottom:3px solid var(--rojo);color:var(--rojo);' : 'color:#888;' ?>">
        Crear cuenta
      </a>
    </div>

    <div style="background:var(--blanco);border-radius:0 0 var(--radio) var(--radio);padding:2rem;box-shadow:var(--sombra);">

      <?php if ($error): ?>
        <div class="alerta alerta-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <?php if ($exito): ?>
        <div class="alerta alerta-exito"><?= htmlspecialchars($exito) ?></div>
      <?php endif; ?>
      <?php $flash = getFlash(); if ($flash): ?>
        <div class="alerta alerta-<?= $flash['tipo'] ?>"><?= htmlspecialchars($flash['msg']) ?></div>
      <?php endif; ?>

      <?php if ($tab === 'login'): ?>
      <!-- LOGIN -->
      <form method="POST">
        <input type="hidden" name="accion" value="login">
        <div class="form-group">
          <label>Correo electrónico</label>
          <input type="email" name="email" placeholder="tucorreo@correo.com" required>
        </div>
        <div class="form-group">
          <label>Contraseña</label>
          <input type="password" name="password" placeholder="········" required>
        </div>
        <button type="submit" class="btn-primary w-full" style="margin-top:.5rem;">Iniciar sesión</button>
        <p style="text-align:center;margin-top:.75rem;font-size:.85rem;">
          <a href="/galezarzal/usuario/olvidoContrasena.php" style="color:var(--rojo);">¿Olvidaste tu contraseña? Restablecer</a>
        </p>
      </form>

      <?php else: ?>
      <!-- REGISTRO inline -->
      <p style="font-size:.88rem;color:var(--gris);margin-bottom:1.25rem;">
        Regístrate como comprador para guardar favoritos y contactar vendedores.
      </p>
      <form method="POST">
        <input type="hidden" name="accion" value="registro">
        <div class="form-row">
          <div class="form-group">
            <label>Nombres *</label>
            <input type="text" name="primerNombre" placeholder="María" required
                   value="<?= htmlspecialchars($_POST['primerNombre'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>Apellidos *</label>
            <input type="text" name="primerApellido" placeholder="Ospina" required
                   value="<?= htmlspecialchars($_POST['primerApellido'] ?? '') ?>">
          </div>
        </div>
        <div class="form-group">
          <label>Usuario *</label>
          <input type="text" name="username" placeholder="@mariaospina" required
                 value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Correo electrónico *</label>
          <input type="email" name="email_reg" placeholder="tu@correo.com" required
                 value="<?= htmlspecialchars($_POST['email_reg'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Teléfono</label>
          <input type="tel" name="celular" placeholder="+57 300..."
                 value="<?= htmlspecialchars($_POST['celular'] ?? '') ?>">
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Contraseña *</label>
            <input type="password" name="password_reg" placeholder="········" required>
          </div>
          <div class="form-group">
            <label>Confirmar contraseña *</label>
            <input type="password" name="confirmPassword" placeholder="········" required>
          </div>
        </div>
        <button type="submit" class="btn-primary w-full">Crear mi cuenta</button>
      </form>
      <?php endif; ?>

    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
