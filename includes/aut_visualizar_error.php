<?php
// ============================================================
//  GALEZARZAL — Visualizar errores en pantalla
// ============================================================
function mostrarError(string $titulo, string $mensaje, string $volver = '/galezarzal/index.php'): void {
    echo '<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Error — Galezarzal</title>
<link rel="stylesheet" href="/galezarzal/assets/css/style.css">
</head>
<body>
<div style="min-height:60vh;display:flex;align-items:center;justify-content:center;">
  <div style="text-align:center;padding:2rem;">
    <h2 style="color:var(--rojo);">' . htmlspecialchars($titulo) . '</h2>
    <p>' . htmlspecialchars($mensaje) . '</p>
    <a href="' . htmlspecialchars($volver) . '" class="btn-primary">Volver</a>
  </div>
</div>
</body>
</html>';
    exit;
}

function flash(string $tipo, string $msg): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['flash'] = ['tipo' => $tipo, 'msg' => $msg];
}

function getFlash(): ?array {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}