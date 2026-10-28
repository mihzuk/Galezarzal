<?php
// ============================================================
//  GALEZARZAL — Cabecera HTML compartida
//  Parámetros: $paginaActiva (inicio|locales|contactos|configuracion)
//              $tituloPagina
// ============================================================
if (session_status() === PHP_SESSION_NONE) session_start();
$_paginaActiva   = $paginaActiva   ?? '';
$_tituloPagina   = $tituloPagina   ?? 'Galezarzal';
$_logueado       = isset($_SESSION['iduser']);
$_nomUsuario     = $_SESSION['username'] ?? '';
$_notifNoLeidas  = 0;
if ($_logueado) {
    try {
        require_once __DIR__ . '/config.php';
        $st = getDB()->prepare("SELECT COUNT(*) FROM Notificacion WHERE iduser=? AND estado='no_leida'");
        $st->execute([$_SESSION['iduser']]);
        $_notifNoLeidas = (int)$st->fetchColumn();
    } catch(Exception $e) { /* silencioso */ }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($_tituloPagina) ?> — Galezarzal</title>
  <link rel="stylesheet" href="/galezarzal/assets/css/style.css">
</head>
<body>

<header class="navbar">
  <a href="/galezarzal/index.php" class="logo">Gale <span>Zarzal</span></a>

  <nav>
    <a href="/galezarzal/index.php"
       class="<?= $_paginaActiva==='inicio'        ? 'activo':'' ?>">Inicio</a>
    <?php if ($_logueado && ($_SESSION['nomRol']??'')==='Vendedor'): ?>
    <a href="/galezarzal/local/miLocal.php"
       class="<?= $_paginaActiva==='milocal'       ? 'activo':'' ?>">Mi Local</a>
    <?php else: ?>
    <a href="/galezarzal/local/buscarLocal.php"
       class="<?= $_paginaActiva==='locales'       ? 'activo':'' ?>">Locales</a>
    <?php endif; ?>
    <a href="/galezarzal/ayudas/consultarFAQ.php"
       class="<?= $_paginaActiva==='contactos'     ? 'activo':'' ?>">Contactos</a>
    <a href="/galezarzal/configuracion/configuracion.php"
       class="<?= $_paginaActiva==='configuracion' ? 'activo':'' ?>">Configuración</a>
  </nav>

  <div class="nav-acciones">
    <a href="/galezarzal/ayudas/consultarFAQ.php" class="btn-icono" title="Ayuda">?</a>

    <?php if ($_logueado): ?>
      <a href="/galezarzal/configuracion/configuracion.php" class="btn-user">
        <?= htmlspecialchars($_nomUsuario) ?>
      </a>
      <a href="/galezarzal/notificaciones/notificaciones.php" class="btn-icono" title="Notificaciones">
        🔔<?= $_notifNoLeidas > 0 ? "<sup style='font-size:.6rem;background:red;border-radius:50%;padding:1px 4px;'>{$_notifNoLeidas}</sup>" : '' ?>
      </a>
    <?php else: ?>
      <a href="/galezarzal/usuario/ingresarCuenta.php" class="btn-user">Iniciar sesión</a>
    <?php endif; ?>
  </div>
</header>