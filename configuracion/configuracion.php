<?php
// ============================================================
//  GALEZARZAL — Configuración / Perfil personal
// ============================================================
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/aut_verifica.php';
require_once __DIR__ . '/../includes/aut_visualizar_error.php';

requiereLogin();

$db     = getDB();
$error  = '';
$exito  = '';

// Cargar datos actuales
$st = $db->prepare(
    "SELECT u.*, c.nomCiudad FROM Usuario u
     LEFT JOIN OrigenUsuario ou ON ou.idOrigenUser = u.idOrigenUser
     LEFT JOIN Ciudad c ON c.idciud = ou.idciud
     WHERE u.iduser = ?"
);
$st->execute([$_SESSION['iduser']]);
$usuario = $st->fetch();

// Guardar cambios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $primerNombre   = trim($_POST['primerNombre']   ?? '');
    $primerApellido = trim($_POST['primerApellido'] ?? '');
    $celular        = trim($_POST['celular']        ?? '');
    $direccion      = trim($_POST['direccion']      ?? '');

    if (!$primerNombre || !$primerApellido) {
        $error = 'El nombre y apellido son obligatorios.';
    } else {
        $db->prepare(
            "UPDATE Usuario SET primerNombre=?, primerApellido=?, celular=?, direccion=?
             WHERE iduser=?"
        )->execute([$primerNombre, $primerApellido, $celular, $direccion, $_SESSION['iduser']]);
        $exito = 'Perfil actualizado correctamente.';
        $st->execute([$_SESSION['iduser']]);
        $usuario = $st->fetch();
    }
}

$paginaActiva   = 'configuracion';
$tituloPagina   = 'Configuración';
$seccionActiva  = 'perfil';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="config-layout">
  <?php require_once __DIR__ . '/sidebar.php'; ?>

  <main class="config-main">
    <div class="config-panel">
      <h2>Perfil personal</h2>

      <?php if ($error): ?><div class="alerta alerta-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
      <?php if ($exito): ?><div class="alerta alerta-exito"><?= htmlspecialchars($exito) ?></div><?php endif; ?>

      <!-- Mini perfil -->
      <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem;">
        <div style="width:56px;height:56px;border-radius:50%;background:var(--rojo);color:var(--blanco);
                    display:flex;align-items:center;justify-content:center;font-size:1.4rem;font-weight:700;">
          <?= strtoupper(substr($usuario['primerNombre']??'G',0,1)) ?>
        </div>
        <div>
          <strong><?= htmlspecialchars(($usuario['primerNombre']??'').' '.($usuario['primerApellido']??'')) ?></strong>
          <p style="font-size:.82rem;color:var(--gris);"><?= htmlspecialchars($usuario['nomRol']??'Comprador') ?> desde <?= date('F Y', strtotime($usuario['fechaRegistro']??'now')) ?></p>
        </div>
      </div>

      <form method="POST">
        <div class="form-row">
          <div class="form-group">
            <label>Nombre</label>
            <input type="text" name="primerNombre" value="<?= htmlspecialchars($usuario['primerNombre']??'') ?>" required>
          </div>
          <div class="form-group">
            <label>Apellido</label>
            <input type="text" name="primerApellido" value="<?= htmlspecialchars($usuario['primerApellido']??'') ?>" required>
          </div>
        </div>
        <div class="form-group">
          <label>Correo</label>
          <input type="email" value="<?= htmlspecialchars($usuario['email']??'') ?>" disabled style="opacity:.6;">
        </div>
        <div class="form-group">
          <label>Teléfono</label>
          <input type="tel" name="celular" value="<?= htmlspecialchars($usuario['celular']??'') ?>" placeholder="+57 316 000 000">
        </div>
        <div class="form-group">
          <label>Dirección</label>
          <input type="text" name="direccion" value="<?= htmlspecialchars($usuario['direccion']??'') ?>" placeholder="Calle, barrio...">
        </div>
        <div class="form-acciones">
          <button type="submit" class="btn-primary">Guardar cambios</button>
          <a href="/galezarzal/index.php" class="btn-outline">Cancelar</a>
        </div>
      </form>

      <div style="margin-top:2rem;padding-top:1rem;border-top:1px solid #eee;">
        <a href="/galezarzal/usuario/eliminarCuenta.php" style="color:#c0392b;font-size:.85rem;">Eliminar mi cuenta</a>
        &nbsp;·&nbsp;
        <a href="/galezarzal/includes/aut_logout.php" style="color:var(--gris);font-size:.85rem;">Cerrar sesión</a>
      </div>
    </div>
  </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>