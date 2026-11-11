<?php
// ============================================================
//  GALEZARZAL — Recuperación de contraseña
//  Tokens de un solo uso con vencimiento. En la base solo se
//  guarda el hash (SHA-256) del token: quien lea la tabla no
//  puede armar enlaces válidos.
//  Las fechas se calculan con NOW() de MySQL/MariaDB, así que
//  no dependen de la zona horaria de PHP.
// ============================================================
require_once __DIR__ . '/config.php';

const TOKEN_MINUTOS = 60; // cuánto dura un enlace

function hashToken(string $token): string {
    return hash('sha256', $token);
}

/**
 * Crea un token nuevo para el usuario y devuelve el token en claro
 * (es la única vez que existe así). Los enlaces pendientes que tuviera
 * antes quedan vencidos, para que solo sirva el más reciente.
 */
function crearTokenRecuperacion(PDO $db, int $iduser): string {
    $db->prepare(
        "UPDATE tokenrecuperacion SET fechaExpira = NOW()
         WHERE iduser = ? AND fechaUso IS NULL AND fechaExpira > NOW()"
    )->execute([$iduser]);

    $token = bin2hex(random_bytes(32)); // 64 caracteres hexadecimales
    $db->prepare(
        "INSERT INTO tokenrecuperacion (iduser, tokenHash, fechaExpira)
         VALUES (?, ?, DATE_ADD(NOW(), INTERVAL " . (int)TOKEN_MINUTOS . " MINUTE))"
    )->execute([$iduser, hashToken($token)]);

    return $token;
}

/**
 * Revisa un token. Devuelve ['estado' => ..., 'idToken' => ..., 'iduser' => ...]
 * con estado: 'valido' | 'usado' | 'vencido' | 'invalido'.
 */
function consultarToken(PDO $db, string $token): array {
    $vacio = ['estado' => 'invalido', 'idToken' => 0, 'iduser' => 0];
    if (!preg_match('/^[0-9a-f]{64}$/', $token)) return $vacio;

    $st = $db->prepare(
        "SELECT t.idToken, t.iduser, t.fechaUso, (t.fechaExpira <= NOW()) AS vencido,
                u.estado AS estadoUsuario
         FROM tokenrecuperacion t
         JOIN usuario u ON u.iduser = t.iduser
         WHERE t.tokenHash = ? LIMIT 1"
    );
    $st->execute([hashToken($token)]);
    $fila = $st->fetch();

    if (!$fila || $fila['estadoUsuario'] !== 'activo') return $vacio;

    $estado = 'valido';
    if ($fila['fechaUso'] !== null)    $estado = 'usado';
    elseif ((int)$fila['vencido'] === 1) $estado = 'vencido';

    return ['estado' => $estado, 'idToken' => (int)$fila['idToken'], 'iduser' => (int)$fila['iduser']];
}

/** Mensaje claro para el usuario según el estado del token. */
function mensajeToken(string $estado): string {
    return match ($estado) {
        'usado'   => 'Este enlace ya se usó para cambiar la contraseña. Si necesitas cambiarla otra vez, pide un enlace nuevo.',
        'vencido' => 'Este enlace venció (dura ' . TOKEN_MINUTOS . ' minutos) o fue reemplazado por uno más reciente. Pide un enlace nuevo.',
        default   => 'Este enlace no es válido. Revisa que lo hayas copiado completo o pide un enlace nuevo.',
    };
}

/** ¿Está activado el modo desarrollo (sin correo)? Por defecto NO: es lo más seguro. */
function modoDesarrollo(): bool {
    return defined('MOSTRAR_ENLACE_RECUPERACION') && MOSTRAR_ENLACE_RECUPERACION === true;
}

/**
 * ¿La petición viene del propio equipo? Se usa la dirección real de la conexión
 * (REMOTE_ADDR), nunca el nombre del sitio ni cabeceras como Host o X-Forwarded-For,
 * porque quien hace la petición puede escribirlas a su gusto.
 */
function peticionLocal(): bool {
    return in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true);
}

/** El enlace solo se muestra en pantalla con el modo desarrollo activo Y desde el propio equipo. */
function enlaceEnPantalla(): bool {
    return modoDesarrollo() && peticionLocal();
}

/** Arma el enlace completo que recibe la persona. */
function enlaceRecuperacion(string $token): string {
    return APP_URL . '/usuario/restablecerContrasena.php?token=' . $token;
}

/**
 * Entrega el enlace. En desarrollo lo guarda en logs/recuperacion.log
 * (la pantalla lo muestra la página). En producción intenta enviarlo por correo.
 */
function entregarEnlaceRecuperacion(string $email, string $enlace): void {
    if (modoDesarrollo()) {
        if (!is_dir(LOG_DIR)) @mkdir(LOG_DIR, 0775, true);
        $ht = LOG_DIR . '/.htaccess';
        if (is_dir(LOG_DIR) && !file_exists($ht)) @file_put_contents($ht, "Require all denied\n");
        $linea = '[' . date('Y-m-d H:i:s') . '] ' . $email . ' ' . $enlace . PHP_EOL;
        @file_put_contents(LOG_DIR . '/recuperacion.log', $linea, FILE_APPEND | LOCK_EX);
        return;
    }

    $asunto = '=?UTF-8?B?' . base64_encode('Restablece tu contraseña de ' . APP_NAME) . '?=';
    $cuerpo = "Recibimos una solicitud para restablecer tu contraseña.\r\n\r\n"
            . "Abre este enlace (dura " . TOKEN_MINUTOS . " minutos y solo sirve una vez):\r\n"
            . $enlace . "\r\n\r\n"
            . "Si no fuiste tú, ignora este mensaje: tu contraseña no cambia.\r\n";
    $cabeceras = "MIME-Version: 1.0\r\nContent-Type: text/plain; charset=UTF-8";
    if (!@mail($email, $asunto, $cuerpo, $cabeceras)) {
        registrarError('No se pudo enviar el correo de recuperación (mail() falló).');
    }
}
