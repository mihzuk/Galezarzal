<?php
// ============================================================
//  GALEZARZAL — Modo mantenimiento (RF-22, Issue #41)
//  Si el administrador activó el modo mantenimiento, cualquier
//  página muestra un aviso (HTTP 503) a visitantes y usuarios que no
//  son administradores. Se ejecuta desde includes/config.php, que
//  todas las páginas cargan primero, así que corta la petición antes
//  de procesar formularios o consultar datos.
//
//  Siguen funcionando durante el mantenimiento:
//   - Los administradores con sesión iniciada (para poder desactivarlo).
//   - usuario/ingresarCuenta.php, para que un administrador pueda entrar
//     (ahí solo se acepta el inicio de sesión de administradores).
//   - includes/aut_logout.php, para cerrar sesión.
// ============================================================
require_once __DIR__ . '/configuracion.php';

/** ¿La página que se está ejecutando es una de las que siguen abiertas? */
function paginaPermitidaEnMantenimiento(): bool {
    $script  = strtolower(str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? ''));
    $abiertas = ['/usuario/ingresarcuenta.php', '/includes/aut_logout.php'];
    foreach ($abiertas as $ruta) {
        if (substr($script, -strlen($ruta)) === $ruta) return true;
    }
    return false;
}

/** Muestra el aviso de mantenimiento y corta la petición. */
function mostrarAvisoMantenimiento(bool $haySesion): void {
    if (!headers_sent()) {
        http_response_code(503);
        header('Retry-After: 3600');
        header('Cache-Control: no-store');
    }
    $nombre = htmlspecialchars(nombreSitio(), ENT_QUOTES, 'UTF-8');
    $enlace = $haySesion
        ? '<a href="/galezarzal/includes/aut_logout.php" class="btn-outline">Cerrar sesión</a>'
        : '<a href="/galezarzal/usuario/ingresarCuenta.php" class="btn-outline">Soy administrador</a>';
    echo '<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>En mantenimiento — ' . $nombre . '</title>
<link rel="stylesheet" href="/galezarzal/assets/css/style.css">
</head>
<body>
<div style="min-height:80vh;display:flex;align-items:center;justify-content:center;">
  <div style="text-align:center;padding:2rem;max-width:520px;">
    <h1 style="font-size:1.6rem;margin-bottom:.75rem;">' . $nombre . ' está en mantenimiento</h1>
    <p style="margin:1rem 0 1.5rem;">Estamos haciendo mejoras en el sitio. Por ahora no puedes usarlo,
       pero volveremos muy pronto. ¡Gracias por tu paciencia!</p>
    ' . $enlace . '
  </div>
</div>
</body>
</html>';
    exit;
}

/** Si el modo mantenimiento está activo, deja pasar solo a quien corresponde. */
function aplicarModoMantenimiento(): void {
    if (PHP_SAPI === 'cli') return;                  // pruebas y scripts por consola
    if (!configActiva('modo_mantenimiento')) return; // sitio funcionando normal

    // Misma configuración de cookie que includes/csrf.php (la sesión se abre aquí primero)
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
        session_start();
    }
    $haySesion = isset($_SESSION['iduser']);
    if ($haySesion && ($_SESSION['nomRol'] ?? '') === 'Administrador') return;
    if (paginaPermitidaEnMantenimiento()) return;

    mostrarAvisoMantenimiento($haySesion);
}
