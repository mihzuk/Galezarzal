<?php
// ============================================================
//  GALEZARZAL — Sidebar de configuración
//  $seccionActiva: perfil|seguridad|notificaciones|privacidad|
//                  favoritos|info-local|productos|horarios|visibilidad|
//                  admin-usuarios|admin-locales|admin-moderacion|admin-mensajes|admin-plataforma
// ============================================================
$_sec = $seccionActiva ?? '';
$_rol = $_SESSION['nomRol'] ?? 'Comprador';
?>
<aside class="sidebar">
  <div class="sidebar-seccion">MI CUENTA</div>
  <a href="<?= htmlspecialchars(BASE_PATH, ENT_QUOTES, 'UTF-8') ?>/configuracion/configuracion.php"          class="<?= $_sec==='perfil'           ?'activo':'' ?>">Perfil personal</a>
  <a href="<?= htmlspecialchars(BASE_PATH, ENT_QUOTES, 'UTF-8') ?>/configuracion/cambiarContrasena.php"      class="<?= $_sec==='seguridad'        ?'activo':'' ?>">Seguridad</a>
  <a href="<?= htmlspecialchars(BASE_PATH, ENT_QUOTES, 'UTF-8') ?>/configuracion/configNotificaciones.php"   class="<?= $_sec==='notificaciones'   ?'activo':'' ?>">Notificaciones</a>
  <a href="<?= htmlspecialchars(BASE_PATH, ENT_QUOTES, 'UTF-8') ?>/configuracion/configPrivacidad.php"       class="<?= $_sec==='privacidad'       ?'activo':'' ?>">Privacidad</a>
  <a href="<?= htmlspecialchars(BASE_PATH, ENT_QUOTES, 'UTF-8') ?>/favoritos/consultarFavoritos.php"         class="<?= $_sec==='favoritos'        ?'activo':'' ?>">Favoritos</a>

  <?php if ($_rol === 'Vendedor' || $_rol === 'Administrador'): ?>
  <div class="sidebar-seccion">MI LOCAL</div>
  <a href="<?= htmlspecialchars(BASE_PATH, ENT_QUOTES, 'UTF-8') ?>/configuracion/infoLocal.php"              class="<?= $_sec==='info-local'       ?'activo':'' ?>">Información</a>
  <a href="<?= htmlspecialchars(BASE_PATH, ENT_QUOTES, 'UTF-8') ?>/configuracion/productos.php"              class="<?= $_sec==='productos'        ?'activo':'' ?>">Productos</a>
  <a href="<?= htmlspecialchars(BASE_PATH, ENT_QUOTES, 'UTF-8') ?>/configuracion/horarios.php"               class="<?= $_sec==='horarios'         ?'activo':'' ?>">Horarios</a>
  <a href="<?= htmlspecialchars(BASE_PATH, ENT_QUOTES, 'UTF-8') ?>/configuracion/visibilidad.php"            class="<?= $_sec==='visibilidad'      ?'activo':'' ?>">Visibilidad</a>
  <?php endif; ?>

  <?php if ($_rol === 'Administrador'): ?>
  <div class="sidebar-seccion">ADMIN</div>
  <a href="<?= htmlspecialchars(BASE_PATH, ENT_QUOTES, 'UTF-8') ?>/admin/gestionarUsuarios.php"              class="<?= $_sec==='admin-usuarios'   ?'activo':'' ?>">Usuarios</a>
  <a href="<?= htmlspecialchars(BASE_PATH, ENT_QUOTES, 'UTF-8') ?>/admin/moderarLocales.php"                 class="<?= $_sec==='admin-locales'    ?'activo':'' ?>">Locales</a>
  <a href="<?= htmlspecialchars(BASE_PATH, ENT_QUOTES, 'UTF-8') ?>/admin/moderarProductos.php"               class="<?= $_sec==='admin-moderacion' ?'activo':'' ?>">Moderación</a>
  <a href="<?= htmlspecialchars(BASE_PATH, ENT_QUOTES, 'UTF-8') ?>/admin/mensajesContacto.php"               class="<?= $_sec==='admin-mensajes'   ?'activo':'' ?>">Mensajes</a>
  <a href="<?= htmlspecialchars(BASE_PATH, ENT_QUOTES, 'UTF-8') ?>/admin/configurarPlataforma.php"           class="<?= $_sec==='admin-plataforma' ?'activo':'' ?>">Plataforma</a>
  <?php endif; ?>
</aside>