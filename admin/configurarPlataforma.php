<?php
// ============================================================
//  GALEZARZAL — Admin: Configuración de la plataforma (RF-22)
// ============================================================
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/aut_verifica.php';
require_once __DIR__ . '/../includes/configuracion.php';

requiereRol(['Administrador']);

$db = getDB();
$exito = isset($_GET['guardado']) ? 'Configuración guardada.' : '';
$error = '';

// Stats de la plataforma
$stats = [];
try {
    $stats['usuarios']  = $db->query("SELECT COUNT(*) FROM usuario WHERE estado='activo'")->fetchColumn();
    $stats['locales']   = $db->query("SELECT COUNT(*) FROM local WHERE estado='activo'")->fetchColumn();
    $stats['revision']  = $db->query("SELECT COUNT(*) FROM local WHERE estado='pendiente'")->fetchColumn();
    $stats['productos'] = $db->query("SELECT COUNT(*) FROM producto WHERE estado='disponible'")->fetchColumn();
} catch(Exception $e) {}

$cfg = obtenerTodaConfig();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo = [
        'nombre_sitio'       => trim($_POST['nombreSitio'] ?? ''),
        'modo_mantenimiento' => isset($_POST['mantenimiento'])    ? '1' : '0',
        'registro_abierto'   => isset($_POST['registroAbierto'])  ? '1' : '0',
        'aprobacion_manual'  => isset($_POST['aprobacionManual']) ? '1' : '0',
    ];

    if ($nuevo['nombre_sitio'] === '') {
        $error = 'El nombre de la plataforma no puede estar vacío.';
    } elseif (mb_strlen($nuevo['nombre_sitio']) > 60) {
        $error = 'El nombre de la plataforma no puede pasar de 60 caracteres.';
    } elseif (guardarConfig($nuevo)) {
        // Post/Redirect/Get: al recargar no se reenvía el formulario
        header('Location: ' . BASE_PATH . '/admin/configurarPlataforma.php?guardado=1');
        exit;
    } else {
        $error = 'No se pudo guardar la configuración. Inténtalo de nuevo.';
    }
    // Si hubo error, se vuelve a mostrar lo que el administrador escribió
    $cfg = array_merge($cfg, $nuevo);
}

$paginaActiva  = 'configuracion';
$tituloPagina  = 'Configuración de plataforma';
$seccionActiva = 'admin-plataforma';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="config-layout">
  <?php require_once __DIR__ . '/../configuracion/sidebar.php'; ?>
  <main class="config-main">
    <div class="config-panel">
      <h2>Panel de administración</h2>
      <?php if ($exito): ?><div class="alerta alerta-exito"><?= htmlspecialchars($exito) ?></div><?php endif; ?>
      <?php if ($error): ?><div class="alerta alerta-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

      <!-- Métricas -->
      <div class="stats-grid" style="margin-bottom:2rem;">
        <div class="stat-card"><div class="num"><?= $stats['usuarios'] ?? 0 ?></div><div class="lbl">Usuarios activos</div></div>
        <div class="stat-card"><div class="num"><?= $stats['locales']  ?? 0 ?></div><div class="lbl">Locales publicados</div></div>
        <div class="stat-card"><div class="num" style="color:var(--alerta);"><?= $stats['revision']  ?? 0 ?></div><div class="lbl">En revisión</div></div>
        <div class="stat-card"><div class="num"><?= $stats['productos']?? 0 ?></div><div class="lbl">Productos activos</div></div>
      </div>

      <!-- Acciones rápidas -->
      <h3 style="font-size:.95rem;font-weight:700;margin-bottom:.75rem;">Acciones rápidas</h3>
      <div style="display:flex;gap:.75rem;flex-wrap:wrap;margin-bottom:2rem;">
        <a href="<?= htmlspecialchars(BASE_PATH, ENT_QUOTES, 'UTF-8') ?>/admin/moderarLocales.php?tab=pendiente" class="btn-primary">
          📋 Revisar locales pendientes <?php if (($stats['revision']??0) > 0): ?>
            <sup style="font-size:.65rem;background:#fff;color:var(--rojo);border-radius:8px;padding:1px 5px;"><?= $stats['revision'] ?></sup>
          <?php endif; ?>
        </a>
        <a href="<?= htmlspecialchars(BASE_PATH, ENT_QUOTES, 'UTF-8') ?>/admin/gestionarUsuarios.php" class="btn-outline">👤 Ver usuarios</a>
        <a href="<?= htmlspecialchars(BASE_PATH, ENT_QUOTES, 'UTF-8') ?>/admin/moderarProductos.php" class="btn-outline">🛍 Moderar productos</a>
      </div>

      <!-- Configuración general -->
      <h3 style="font-size:.95rem;font-weight:700;margin-bottom:.75rem;">Configuración general</h3>
      <div class="form-wrap" style="max-width:100%;">
        <form method="POST">
          <div class="form-group">
            <label>Nombre de la plataforma</label>
            <input type="text" name="nombreSitio" maxlength="60" required
                   value="<?= htmlspecialchars($cfg['nombre_sitio']) ?>">
          </div>
          <div class="toggle-row" style="margin-bottom:1rem;">
            <div class="toggle-info">
              <h4>Modo mantenimiento</h4>
              <p>Los visitantes y usuarios que no son administradores solo ven un aviso</p>
            </div>
            <label class="toggle"><input type="checkbox" name="mantenimiento" <?= $cfg['modo_mantenimiento'] === '1' ? 'checked' : '' ?>><span class="slider"></span></label>
          </div>
          <div class="toggle-row" style="margin-bottom:1rem;">
            <div class="toggle-info">
              <h4>Registro abierto</h4>
              <p>Permite que nuevos usuarios se registren</p>
            </div>
            <label class="toggle"><input type="checkbox" name="registroAbierto" <?= $cfg['registro_abierto'] === '1' ? 'checked' : '' ?>><span class="slider"></span></label>
          </div>
          <div class="toggle-row" style="margin-bottom:1.25rem;">
            <div class="toggle-info">
              <h4>Aprobación manual</h4>
              <p>Los locales nuevos esperan la aprobación de un administrador</p>
            </div>
            <label class="toggle"><input type="checkbox" name="aprobacionManual" <?= $cfg['aprobacion_manual'] === '1' ? 'checked' : '' ?>><span class="slider"></span></label>
          </div>
          <button type="submit" class="btn-primary">Guardar configuración</button>
        </form>
      </div>
    </div>
  </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
