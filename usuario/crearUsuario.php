<?php
// ============================================================
//  GALEZARZAL — Crear cuenta (registro de comprador)
// ============================================================
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/aut_visualizar_error.php';
require_once __DIR__ . '/../includes/validaciones.php';

$error   = '';
$exito   = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $primerNombre    = trim($_POST['primerNombre']    ?? '');
    $primerApellido  = trim($_POST['primerApellido']  ?? '');
    $username        = trim($_POST['username']        ?? '');
    $email           = trim($_POST['email']           ?? '');
    $celular         = trim($_POST['celular']         ?? '');
    $password        = $_POST['password']             ?? '';
    $confirmPassword = $_POST['confirmPassword']      ?? '';

    // Validación en el servidor (largos, formatos y contraseña)
    $error = validarDatosRegistro([
        'primerNombre'    => $primerNombre,
        'primerApellido'  => $primerApellido,
        'username'        => $username,
        'email'           => $email,
        'celular'         => $celular,
        'password'        => $password,
        'confirmPassword' => $confirmPassword,
    ]);

    if ($error === '') {
        $db = getDB();
        // Verificar duplicados
        $st = $db->prepare("SELECT COUNT(*) FROM Usuario WHERE email=? OR username=?");
        $st->execute([$email, $username]);
        if ($st->fetchColumn() > 0) {
            $error = 'El correo o nombre de usuario ya está registrado.';
        } else {
            // idTipDocu default = 1 (CC), idRol = 1 (Comprador)
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins  = $db->prepare(
                "INSERT INTO Usuario (username, email, passwordHash, primerNombre, primerApellido, celular, idTipDocu, idRol)
                 VALUES (?,?,?,?,?,?,1,1)"
            );
            // El celular se guarda normalizado (10 dígitos) o NULL si lo dejó vacío
            $ins->execute([$username, $email, $hash, $primerNombre, $primerApellido,
                           normalizarCelular($celular) ?: null]);
            $exito = true;
        }
    }
}

$paginaActiva = '';
$tituloPagina = 'Crear cuenta';
require_once __DIR__ . '/../includes/header.php';
?>

<section style="min-height:70vh;display:flex;align-items:center;justify-content:center;padding:2rem;">
  <div class="form-wrap" style="max-width:540px;">
    <h2 class="form-titulo">Crear cuenta en Galezarzal</h2>
    <p style="font-size:.88rem;color:var(--gris);margin-bottom:1.25rem;">
      Regístrate como comprador para guardar favoritos y contactar vendedores.
    </p>

    <?php if ($exito): ?>
      <div class="alerta alerta-exito">¡Cuenta creada con éxito! <a href="/galezarzal/usuario/ingresarCuenta.php" style="color:var(--rojo);font-weight:600;">Iniciar sesión</a></div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="alerta alerta-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (!$exito): ?>
    <form method="POST">
      <div class="form-row">
        <div class="form-group">
          <label>Nombres *</label>
          <input type="text" name="primerNombre" placeholder="María" required minlength="2" maxlength="50" value="<?= htmlspecialchars($_POST['primerNombre'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Apellidos *</label>
          <input type="text" name="primerApellido" placeholder="Ospina" required minlength="2" maxlength="50" value="<?= htmlspecialchars($_POST['primerApellido'] ?? '') ?>">
        </div>
      </div>
      <div class="form-group">
        <label>Usuario *</label>
        <input type="text" name="username" placeholder="mariaospina" required minlength="3" maxlength="30"
               pattern="[A-Za-z0-9._]+" title="Letras, números, punto y guion bajo. Sin espacios."
               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Correo electrónico *</label>
        <input type="email" name="email" placeholder="tu@correo.com" required maxlength="100" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Teléfono</label>
        <input type="tel" name="celular" placeholder="+57 300 123 4567" maxlength="20"
               pattern="(\+?57[\s\-]?)?3\d{2}[\s\-]?\d{3}[\s\-]?\d{4}"
               title="Celular colombiano de 10 dígitos que empiece por 3. Ej: 300 123 4567"
               value="<?= htmlspecialchars($_POST['celular'] ?? '') ?>">
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Contraseña *</label>
          <input type="password" name="password" placeholder="········" required minlength="6" maxlength="72">
        </div>
        <div class="form-group">
          <label>Confirmar contraseña *</label>
          <input type="password" name="confirmPassword" placeholder="········" required minlength="6" maxlength="72">
        </div>
      </div>
      <div class="form-acciones">
        <button type="submit" class="btn-primary">Crear mi cuenta</button>
        <a href="/galezarzal/usuario/ingresarCuenta.php" class="btn-outline">¿Ya tienes cuenta? Inicia sesión</a>
      </div>
    </form>
    <?php endif; ?>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>