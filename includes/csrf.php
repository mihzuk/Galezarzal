<?php
// ============================================================
//  GALEZARZAL — Protección CSRF
//  1) Genera un token por sesión.
//  2) Lo inserta solo en todo <form method="POST"> de la página.
//  3) Rechaza (403) cualquier POST sin un token válido.
// ============================================================

// La sesión guarda el token; se marca la cookie como más segura
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true,   // JavaScript no puede leer la cookie
        'samesite' => 'Lax',  // no se envía en POST desde otros sitios
    ]);
    session_start();
}

/** Devuelve el token de la sesión (lo crea si no existe). */
function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Devuelve el <input> oculto con el token (por si se quiere usar a mano). */
function csrfCampo(): string {
    return '<input type="hidden" name="csrf_token" value="'
         . htmlspecialchars(csrfToken()) . '">';
}

/** Comprueba que el token enviado coincide con el de la sesión. */
function csrfValido(): bool {
    $enviado  = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    $guardado = $_SESSION['csrf_token'] ?? '';
    // hash_equals compara sin filtrar información por tiempos de respuesta
    return is_string($enviado) && $guardado !== '' && hash_equals($guardado, $enviado);
}

/** Muestra un mensaje claro y corta la petición con HTTP 403. */
function csrfRechazar(): void {
    http_response_code(403);
    echo '<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Solicitud no válida — Galezarzal</title>
<link rel="stylesheet" href="/galezarzal/assets/css/style.css">
</head>
<body>
<div style="min-height:60vh;display:flex;align-items:center;justify-content:center;">
  <div style="text-align:center;padding:2rem;max-width:480px;">
    <h2 style="color:var(--rojo);">Solicitud no válida</h2>
    <p style="margin:1rem 0;">No pudimos verificar que esta acción vino de nuestro sitio
       (el formulario expiró o falta el token de seguridad).
       Vuelve atrás, recarga la página e inténtalo de nuevo.</p>
    <a href="javascript:history.back()" class="btn-primary">Volver</a>
  </div>
</div>
</body>
</html>';
    exit;
}

/** Agrega el campo oculto a cada <form method="POST"> del HTML. */
function csrfInyectar(string $html, string $token): string {
    $campo = '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    return preg_replace_callback('/<form\b[^>]*>/i', function ($m) use ($campo) {
        $tag = $m[0];
        // Solo formularios con method="POST"
        if (!preg_match('/\bmethod\s*=\s*["\']?post\b/i', $tag)) return $tag;
        // Nunca se pone el token en formularios que apuntan a otro sitio
        if (preg_match('/\baction\s*=\s*["\']?(https?:)?\/\//i', $tag)) return $tag;
        return $tag . $campo;
    }, $html);
}

// 1) Validar toda petición que modifica datos (ANTES de procesar el formulario)
$_csrfMetodo = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if (in_array($_csrfMetodo, ['POST', 'PUT', 'PATCH', 'DELETE'], true) && !csrfValido()) {
    csrfRechazar();
}

// 2) Insertar el token en los formularios de la página que se va a mostrar
$_csrfToken = csrfToken();
ob_start(function (string $html) use ($_csrfToken) {
    return csrfInyectar($html, $_csrfToken);
});