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

// Ajustes locales opcionales: se cargan antes de calcular APP_URL.
// No se sube config.local.php a Git.
if (is_file(__DIR__ . '/config.local.php')) {
    require __DIR__ . '/config.local.php';
}

// Configura la URL en una sola ubicación: variable de entorno APP_URL
// o constante APP_URL_OVERRIDE definida en config.local.php.
// El valor predeterminado conserva la instalación original de XAMPP.
$_appUrl = getenv('APP_URL') ?: (
    defined('APP_URL_OVERRIDE') ? APP_URL_OVERRIDE : 'http://localhost/galezarzal'
);
define('APP_URL', rtrim($_appUrl, '/'));
unset($_appUrl);

// Prefijo de rutas para enlaces, redirecciones y recursos estáticos.
define('BASE_PATH', rtrim(parse_url(APP_URL, PHP_URL_PATH) ?: '', '/'));

// Recuperación de contraseña. Por defecto (false) el enlace se envía por correo
// y NUNCA se muestra en pantalla. Solo en tu equipo, creando config.local.php,
// puede valer true: entonces no se envía correo y el enlace aparece en pantalla
// (solo si abres el sitio desde el mismo equipo) y en logs/recuperacion.log.
if (!defined('MOSTRAR_ENLACE_RECUPERACION')) {
    define('MOSTRAR_ENLACE_RECUPERACION', false);
}

// Correo de los mensajes de contacto (RF-24). CORREO_EQUIPO: quién los recibe
// (uno o varios correos separados por coma). CORREO_REMITENTE (opcional): desde
// qué dirección sale, útil en hostings que exigen una del dominio. Se definen en
// includes/config.local.php o con variables de entorno del servidor; si
// CORREO_EQUIPO queda vacío, el mensaje se guarda igual y el aviso va a logs/errores.log.
if (!defined('CORREO_EQUIPO')) {
    define('CORREO_EQUIPO', getenv('CORREO_EQUIPO') ?: '');
}
if (!defined('CORREO_REMITENTE')) {
    define('CORREO_REMITENTE', getenv('CORREO_REMITENTE') ?: '');
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

// Modo mantenimiento (RF-22): al final, cuando getDB() y las funciones ya existen.
require_once __DIR__ . '/mantenimiento.php';
aplicarModoMantenimiento();
