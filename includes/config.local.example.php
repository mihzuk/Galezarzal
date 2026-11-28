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

// ------------------------------------------------------------
// Envío real de la recuperación de contraseña (HT #11). En tu equipo basta el
// modo desarrollo de arriba; esto es para probar el envío de verdad o para el
// servidor donde publiques el sitio. Con SMTP_HOST el sitio envía por SMTP; sin
// él usa mail(). CORREO_REMITENTE (arriba) es el remitente de estos correos y,
// si no lo defines, se usa SMTP_USUARIO.
// Ejemplo con Gmail (cuenta con verificación en dos pasos y una "contraseña de
// aplicación"; NO uses la contraseña normal de la cuenta):
//
// define('SMTP_HOST', 'smtp.gmail.com');
// define('SMTP_PORT', 587);
// define('SMTP_SEGURIDAD', 'tls');          // 'tls' (587), 'ssl' (465) o 'ninguna'
// define('SMTP_USUARIO', 'tu_cuenta@gmail.com');
// define('SMTP_CLAVE', 'tu-contraseña-de-aplicación');
//
// Con el modo desarrollo activo (MOSTRAR_ENLACE_RECUPERACION = true) no se envía
// correo: para probar el envío real, pon esa constante en false o bórrala.
// En un contenedor o hosting puedes usar variables de entorno con los mismos
// nombres (SMTP_HOST, SMTP_PORT, SMTP_SEGURIDAD, SMTP_USUARIO, SMTP_CLAVE), así la
// contraseña no vive en ningún archivo del proyecto.
