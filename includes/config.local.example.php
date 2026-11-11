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
