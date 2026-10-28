<?php
// ============================================================
//  GALEZARZAL — Cerrar sesión
// ============================================================
if (session_status() === PHP_SESSION_NONE) session_start();
$_SESSION = [];
session_destroy();
header('Location: /galezarzal/index.php');
exit;