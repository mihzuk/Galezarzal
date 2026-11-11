<?php
// ============================================================
//  GALEZARZAL — Configuración de Base de Datos
// ============================================================
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'galezarzal');
define('DB_CHARSET', 'utf8mb4');

define('APP_NAME', 'Galezarzal');
define('APP_URL', 'http://localhost/galezarzal');

// Ajustes de tu equipo (no se suben a Git). Si existe includes/config.local.php
// se carga aquí; para activar el modo desarrollo copia config.local.example.php.
if (is_file(__DIR__ . '/config.local.php')) {
    require __DIR__ . '/config.local.php';
}

// Recuperación de contraseña. Por defecto (false) el enlace se envía por correo
// y NUNCA se muestra en pantalla. Solo en tu equipo, creando config.local.php,
// puede valer true: entonces no se envía correo y el enlace aparece en pantalla
// (solo si abres el sitio desde el mismo equipo) y en logs/recuperacion.log.
if (!defined('MOSTRAR_ENLACE_RECUPERACION')) {
    define('MOSTRAR_ENLACE_RECUPERACION', false);
}

// Carpeta y archivo del log (ignorados por Git)
define('LOG_DIR', __DIR__ . '/../logs');
define('LOG_ARCHIVO', LOG_DIR . '/errores.log');

/**
 * Guarda un error técnico en logs/errores.log (solo local).
 * El usuario nunca ve este detalle.
 */
function registrarError(string $mensaje): void {
    // Crear la carpeta si no existe
    if (!is_dir(LOG_DIR)) {
        @mkdir(LOG_DIR, 0775, true);
    }
    // Bloquear el acceso web a la carpeta (Apache 2.4)
    $ht = LOG_DIR . '/.htaccess';
    if (is_dir(LOG_DIR) && !file_exists($ht)) {
        @file_put_contents($ht, "Require all denied\n");
    }
    // Una sola línea por error, con fecha y hora
    $mensaje = str_replace(["\r", "\n"], ' ', $mensaje);
    $linea   = '[' . date('Y-m-d H:i:s') . '] ' . $mensaje . PHP_EOL;
    // Si no se puede escribir el archivo, se usa el log de Apache/PHP
    if (@file_put_contents(LOG_ARCHIVO, $linea, FILE_APPEND | LOCK_EX) === false) {
        error_log($mensaje);
    }
}

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // El detalle técnico va solo al log
            registrarError('Fallo de conexión a la BD: ' . $e->getMessage());
            // Al usuario se le muestra un mensaje genérico
            if (!headers_sent()) {
                http_response_code(500);
            }
            die('<div style="padding:2rem;font-family:sans-serif;background:#1A1A2E;color:#E94560;text-align:center;">
                <h2>No pudimos cargar la página</h2>
                <p>Estamos teniendo problemas técnicos. Intenta de nuevo en unos minutos.</p>
            </div>');
        }
    }
    return $pdo;
}