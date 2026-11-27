<?php
// ============================================================
//  GALEZARZAL — Ajustes locales de ejemplo
//  Copia este archivo como includes/config.local.php (Git lo ignora).
//  NO lo copies al servidor donde publiques el sitio.
// ============================================================

// Modo desarrollo de la recuperación de contraseña: no se envía correo; el
// enlace aparece en pantalla (solo si abres el sitio desde este mismo equipo)
// y se guarda en logs/recuperacion.log.
define('MOSTRAR_ENLACE_RECUPERACION', true);

// Correo de los mensajes de contacto (RF-24). Quita el // de la línea que necesites.
// En XAMPP no hay servidor de correo: con esto vacío el mensaje igual se guarda y
// aparece en el panel de administración, y el aviso queda en logs/errores.log.
// define('CORREO_EQUIPO', 'equipo@tudominio.com');      // uno o varios, separados por coma
// define('CORREO_REMITENTE', 'no-responder@tudominio.com'); // opcional


// Credenciales de la base de datos. En un servidor publicado usa un usuario
// propio con contraseña; no uses root sin contraseña.
// define('DB_HOST', 'localhost');
// define('DB_USER', 'galezarzal_app');
// define('DB_PASS', 'cambia-esta-clave');
// define('DB_NAME', 'galezarzal');
