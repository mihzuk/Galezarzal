<?php
// ============================================================
//  GALEZARZAL — Sidebar de configuración
//  $seccionActiva: perfil|seguridad|notificaciones|privacidad|
//                  favoritos|info-local|productos|horarios|visibilidad|
//                  admin-usuarios|admin-locales|admin-moderacion|admin-plataforma
// ============================================================
$_sec = $seccionActiva ?? '';
$_rol = $_SESSION['nomRol'] ?? 'Comprador';
?>
<aside class="sidebar">
  <div class="sidebar-seccion">MI CUENTA</div>
  <a href="/galezarzal/configuracion/configuracion.php"          class="<?= $_sec==='perfil'           ?'activo':'' ?>">Perfil personal</a>
  <a href="/galezarzal/configuracion/cambiarContrasena.php"      class="<?= $_sec==='seguridad'        ?'activo':'' ?>">Seguridad</a>
  <a href="/galezarzal/configuracion/configNotificaciones.php"   class="<?= $_sec==='notificaciones'   ?'activo':'' ?>">Notificaciones</a>
  <a href="/galezarzal/configuracion/configPrivacidad.php"       class="<?= $_sec==='privacidad'       ?'activo':'' ?>">Privacidad</a>
  <a href="/galezarzal/favoritos/consultarFavoritos.php"         class="<?= $_sec==='favoritos'        ?'activo':'' ?>">Favoritos</a>

  <?php if ($_rol === 'Vendedor' || $_rol === 'Administrador'): ?>
  <div class="sidebar-seccion">MI LOCAL</div>
  <a href="/galezarzal/configuracion/infoLocal.php"              class="<?= $_sec==='info-local'       ?'activo':'' ?>">Información</a>
  <a href="/galezarzal/configuracion/productos.php"              class="<?= $_sec==='productos'        ?'activo':'' ?>">Productos</a>
  <a href="/galezarzal/configuracion/horarios.php"               class="<?= $_sec==='horarios'         ?'activo':'' ?>">Horarios</a>
  <a href="/galezarzal/configuracion/visibilidad.php"            class="<?= $_sec==='visibilidad'      ?'activo':'' ?>">Visibilidad</a>
  <?php endif; ?>

  <?php if ($_rol === 'Administrador'): ?>
  <div class="sidebar-seccion">ADMIN</div>
  <a href="/galezarzal/admin/gestionarUsuarios.php"              class="<?= $_sec==='admin-usuarios'   ?'activo':'' ?>">Usuarios</a>
  <a href="/galezarzal/admin/moderarLocales.php"                 class="<?= $_sec==='admin-locales'    ?'activo':'' ?>">Locales</a>
  <a href="/galezarzal/admin/moderarProductos.php"               class="<?= $_sec==='admin-moderacion' ?'activo':'' ?>">Moderación</a>
  <a href="/galezarzal/admin/configurarPlataforma.php"           class="<?= $_sec==='admin-plataforma' ?'activo':'' ?>">Plataforma</a>
  <?php endif; ?>
</aside>