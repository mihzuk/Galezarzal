<?php
// ============================================================
//  GALEZARZAL — Ajustes locales de ejemplo
//  Copia este archivo como includes/config.local.php (Git lo ignora).
//  NO lo copies al servidor donde publiques el sitio.
// ============================================================

// OPCIONAL: descomenta y cambia la URL para instalar en otra carpeta
// o dominio, sin modificar includes/config.php.
// define('APP_URL_OVERRIDE', 'http://localhost/mitienda');
// define('APP_URL_OVERRIDE', 'https://midominio.com');

// Modo desarrollo de la recuperación de contraseña: no se envía correo; el
// enlace aparece en pantalla (solo si abres el sitio desde este mismo equipo)
// y se guarda en logs/recuperacion.log.
define('MOSTRAR_ENLACE_RECUPERACION', true);
