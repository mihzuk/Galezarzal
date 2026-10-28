<?php
// ============================================================
//  GALEZARZAL — Admin: Configuración de la plataforma
// ============================================================
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/aut_verifica.php';

requiereRol(['Administrador']);

$db = getDB();
$exito = '';

// Stats de la plataforma
$stats = [];
try {
    $stats['usuarios']  = $db->query("SELECT COUNT(*) FROM Usuario WHERE estado='activo'")->fetchColumn();
    $stats['locales']   = $db->query("SELECT COUNT(*) FROM Local WHERE estado='activo'")->fetchColumn();
    $stats['revision']  = $db->query("SELECT COUNT(*) FROM Local WHERE estado='pendiente'")->fetchColumn();
    $stats['productos'] = $db->query("SELECT COUNT(*) FROM Producto WHERE estado='disponible'")->fetchColumn();
} catch(Exception $e) {}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Aquí se guardarían configuraciones de plataforma (nombre del sitio, etc.)
    $exito = 'Configuración guardada.';
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
        <a href="/galezarzal/admin/moderarLocales.php?tab=pendiente" class="btn-primary">
          📋 Revisar locales pendientes <?php if (($stats['revision']??0) > 0): ?>
            <sup style="font-size:.65rem;background:#fff;color:var(--rojo);border-radius:8px;padding:1px 5px;"><?= $stats['revision'] ?></sup>
          <?php endif; ?>
        </a>
        <a href="/galezarzal/admin/gestionarUsuarios.php" class="btn-outline">👤 Ver usuarios</a>
        <a href="/galezarzal/admin/moderarProductos.php" class="btn-outline">🛍 Moderar productos</a>
      </div>

      <!-- Configuración general -->
      <h3 style="font-size:.95rem;font-weight:700;margin-bottom:.75rem;">Configuración general</h3>
      <div class="form-wrap" style="max-width:100%;">
        <form method="POST">
          <div class="form-group">
            <label>Nombre de la plataforma</label>
            <input type="text" name="appNombre" value="Galezarzal">
          </div>
          <div class="form-group">
            <label>URL del sitio</label>
            <input type="url" name="appUrl" value="http://localhost/galezarzal">
          </div>
          <div class="toggle-row" style="margin-bottom:1rem;">
            <div class="toggle-info">
              <h4>Modo mantenimiento</h4>
              <p>Muestra un mensaje a los visitantes</p>
            </div>
            <label class="toggle"><input type="checkbox" name="mantenimiento"><span class="slider"></span></label>
          </div>
          <div class="toggle-row" style="margin-bottom:1.25rem;">
            <div class="toggle-info">
              <h4>Registro abierto</h4>
              <p>Permite que nuevos usuarios se registren</p>
            </div>
            <label class="toggle"><input type="checkbox" name="registroAbierto" checked><span class="slider"></span></label>
          </div>
          <button type="submit" class="btn-primary">Guardar configuración</button>
        </form>
      </div>
    </div>
  </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
