<?php
// ============================================================
//  GALEZARZAL — Envío de correo por SMTP (HT #11, RF-04)
//  Envía el correo de recuperación de contraseña. (Los correos de los
//  mensajes de contacto, RF-24, siguen en includes/correo.php.)
//
//  Dos formas de enviar, según la configuración:
//   - Si hay SMTP_HOST configurado: envía por SMTP (Gmail, Outlook,
//     el correo del hosting, etc.) con una conexión cifrada.
//   - Si no: usa mail() de PHP, que solo funciona si el servidor
//     tiene correo configurado.
//
//  Cada ajuste se busca primero como constante (includes/config.local.php,
//  que Git ignora) y después como variable de entorno (útil en contenedores
//  y hostings). Así la contraseña del correo nunca queda en el código:
//
//    SMTP_HOST         servidor, por ejemplo smtp.gmail.com
//    SMTP_PORT         587 (por defecto) o 465
//    SMTP_SEGURIDAD    'tls' (STARTTLS, por defecto), 'ssl' (puerto 465) o 'ninguna'
//    SMTP_USUARIO      cuenta con la que se inicia sesión
//    SMTP_CLAVE        contraseña (en Gmail, una "contraseña de aplicación")
//    CORREO_REMITENTE  dirección que aparece como remitente (por defecto, SMTP_USUARIO;
//                      es el mismo ajuste que usan los mensajes de contacto)
//    CORREO_REMITENTE_NOMBRE  nombre del remitente (por defecto, el nombre del sitio)
//
//  enviarCorreo() devuelve true si el servidor aceptó el mensaje y false si
//  no se pudo enviar. El detalle técnico del fallo va a logs/errores.log, sin
//  contraseñas ni enlaces.
// ============================================================
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/configuracion.php'; // nombre del sitio

/** Lee un ajuste: constante > variable de entorno > valor por defecto. */
function correoAjuste(string $nombre, $porDefecto = null) {
    if (defined($nombre)) return constant($nombre);
    $valor = getenv($nombre);
    return ($valor !== false && $valor !== '') ? $valor : $porDefecto;
}

/** Ajustes de correo ya normalizados. */
function correoConfiguracion(): array {
    $host      = trim((string)correoAjuste('SMTP_HOST', ''));
    $seguridad = strtolower(trim((string)correoAjuste('SMTP_SEGURIDAD', '')));
    $puerto    = (int)correoAjuste('SMTP_PORT', 0);
    if ($puerto <= 0) $puerto = ($seguridad === 'ssl') ? 465 : 587;
    if (!in_array($seguridad, ['tls', 'ssl', 'ninguna'], true)) {
        $seguridad = ($puerto === 465) ? 'ssl' : 'tls';
    }

    $usuario   = (string)correoAjuste('SMTP_USUARIO', '');
    $remitente = trim((string)correoAjuste('CORREO_REMITENTE', ''));
    if ($remitente === '') {
        $remitente = filter_var($usuario, FILTER_VALIDATE_EMAIL)
            ? $usuario
            : 'no-responder@' . (parse_url(APP_URL, PHP_URL_HOST) ?: 'localhost');
    }

    return [
        'smtp'      => $host !== '',
        'host'      => $host,
        'puerto'    => $puerto,
        'seguridad' => $seguridad,
        'usuario'   => $usuario,
        'clave'     => (string)correoAjuste('SMTP_CLAVE', ''),
        'remitente' => $remitente,
        'nombre'    => (string)correoAjuste('CORREO_REMITENTE_NOMBRE', nombreSitio()),
        // Con mail() solo se agrega el "From" si el equipo lo configuró a propósito
        'remitenteExplicito' => trim((string)correoAjuste('CORREO_REMITENTE', '')) !== '',
    ];
}

/** Quita saltos de línea de un valor que va en una cabecera (evita inyección de cabeceras). */
function limpiarCabecera(string $valor): string {
    return trim(preg_replace('/[\r\n]+/', ' ', $valor));
}

/** Codifica un texto para cabecera si tiene tildes, ñ, etc. (si ya viene codificado, lo deja). */
function codificarCabecera(string $texto): string {
    $texto = limpiarCabecera($texto);
    if (str_starts_with($texto, '=?UTF-8?')) return $texto;
    if (!preg_match('/[^\x20-\x7E]/', $texto)) return $texto;
    return '=?UTF-8?B?' . base64_encode($texto) . '?=';
}

/**
 * Envía un correo de texto plano. true = el servidor lo aceptó; false = falló.
 * Nunca lanza excepciones ni muestra nada: el que llama decide qué decirle a la persona.
 */
function enviarCorreo(string $para, string $asunto, string $cuerpo): bool {
    // Un destinatario con saltos de línea o inválido nunca se envía
    if (preg_match('/[\r\n]/', $para) || !filter_var($para, FILTER_VALIDATE_EMAIL)) {
        registrarError('Correo: destinatario inválido, no se envió.');
        return false;
    }
    $c = correoConfiguracion();
    return $c['smtp']
        ? enviarPorSmtp($para, $asunto, $cuerpo, $c)
        : enviarConMail($para, $asunto, $cuerpo, $c);
}

/** Envío con mail() de PHP (necesita correo configurado en el servidor). */
function enviarConMail(string $para, string $asunto, string $cuerpo, array $c): bool {
    $cabeceras = "MIME-Version: 1.0\r\nContent-Type: text/plain; charset=UTF-8";
    if ($c['remitenteExplicito']) {
        $cabeceras .= "\r\nFrom: " . codificarCabecera($c['nombre']) . ' <' . limpiarCabecera($c['remitente']) . '>';
    }
    if (!@mail($para, codificarCabecera($asunto), $cuerpo, $cabeceras)) {
        registrarError('Correo: mail() falló. El servidor no tiene correo configurado; configura SMTP_HOST (ver includes/smtp.php).');
        return false;
    }
    return true;
}

/** Lee una respuesta SMTP completa (puede venir en varias líneas). */
function smtpLeer($fp): array {
    $texto = '';
    while (($linea = fgets($fp, 1024)) !== false) {
        $texto .= $linea;
        if (strlen($linea) < 4 || $linea[3] !== '-') {
            return [(int)substr($linea, 0, 3), trim($texto)];
        }
    }
    $meta = stream_get_meta_data($fp);
    throw new RuntimeException(!empty($meta['timed_out'])
        ? 'el servidor tardó demasiado en responder'
        : 'el servidor cerró la conexión');
}

/** Manda una orden y exige uno de los códigos esperados. $etiqueta nunca lleva datos secretos. */
function smtpOrden($fp, string $orden, array $esperados, string $etiqueta): array {
    if (fwrite($fp, $orden . "\r\n") === false) throw new RuntimeException("no se pudo enviar $etiqueta");
    [$codigo, $texto] = smtpLeer($fp);
    if (!in_array($codigo, $esperados, true)) {
        throw new RuntimeException("el servidor rechazó $etiqueta ($codigo " . substr(preg_replace('/\s+/', ' ', $texto), 0, 120) . ')');
    }
    return [$codigo, $texto];
}

/** Envío por SMTP con conexión cifrada y certificado verificado. */
function enviarPorSmtp(string $para, string $asunto, string $cuerpo, array $c): bool {
    $host = $c['host'];
    $esLocal = in_array(strtolower($host), ['localhost', '127.0.0.1', '::1'], true);
    if ($c['usuario'] !== '' && $c['seguridad'] === 'ninguna' && !$esLocal) {
        registrarError('Correo: no se envía la contraseña SMTP sin cifrado. Usa SMTP_SEGURIDAD tls o ssl.');
        return false;
    }

    $contexto = stream_context_create(['ssl' => [
        'verify_peer' => true, 'verify_peer_name' => true, 'peer_name' => $host, 'SNI_enabled' => true,
    ]]);
    $destino = ($c['seguridad'] === 'ssl' ? 'ssl://' : 'tcp://') . $host . ':' . $c['puerto'];
    $errno = 0; $errstr = '';
    $fp = @stream_socket_client($destino, $errno, $errstr, 8, STREAM_CLIENT_CONNECT, $contexto);
    if (!$fp) {
        registrarError("Correo SMTP: no se pudo conectar a $host:{$c['puerto']} ($c[seguridad]): " . ($errstr ?: 'sin detalle'));
        return false;
    }
    stream_set_timeout($fp, 8);

    try {
        [$codigo, $texto] = smtpLeer($fp);
        if ($codigo !== 220) throw new RuntimeException("saludo inesperado ($codigo)");

        $yo = parse_url(APP_URL, PHP_URL_HOST) ?: 'localhost';
        [, $capacidades] = smtpOrden($fp, 'EHLO ' . $yo, [250], 'EHLO');

        if ($c['seguridad'] === 'tls') {
            smtpOrden($fp, 'STARTTLS', [220], 'STARTTLS');
            if (!@stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                throw new RuntimeException('no se pudo establecer la conexión cifrada (revisa el certificado del servidor)');
            }
            [, $capacidades] = smtpOrden($fp, 'EHLO ' . $yo, [250], 'EHLO');
        }

        if ($c['usuario'] !== '') {
            if (stripos($capacidades, 'PLAIN') !== false) {
                smtpOrden($fp, 'AUTH PLAIN ' . base64_encode("\0" . $c['usuario'] . "\0" . $c['clave']), [235], 'el inicio de sesión (usuario o contraseña SMTP)');
            } else {
                smtpOrden($fp, 'AUTH LOGIN', [334], 'AUTH LOGIN');
                smtpOrden($fp, base64_encode($c['usuario']), [334], 'el usuario SMTP');
                smtpOrden($fp, base64_encode($c['clave']), [235], 'el inicio de sesión (usuario o contraseña SMTP)');
            }
        }

        $remitente = limpiarCabecera($c['remitente']);
        smtpOrden($fp, 'MAIL FROM:<' . $remitente . '>', [250], 'el remitente');
        smtpOrden($fp, 'RCPT TO:<' . $para . '>', [250, 251], 'el destinatario');
        smtpOrden($fp, 'DATA', [354], 'DATA');

        $mensaje = implode("\r\n", [
            'Date: ' . date('r'),
            'From: ' . codificarCabecera($c['nombre']) . ' <' . $remitente . '>',
            'To: <' . $para . '>',
            'Subject: ' . codificarCabecera($asunto),
            'Message-ID: <' . bin2hex(random_bytes(12)) . '@' . $yo . '>',
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=UTF-8',
            'Content-Transfer-Encoding: base64',
            'Auto-Submitted: auto-generated',
            '',
            rtrim(chunk_split(base64_encode($cuerpo), 76, "\r\n")),
        ]);
        $mensaje = preg_replace('/^\./m', '..', $mensaje); // "dot-stuffing" del protocolo SMTP
        smtpOrden($fp, $mensaje . "\r\n.", [250], 'el mensaje');

        @fwrite($fp, "QUIT\r\n");
        return true;
    } catch (RuntimeException $e) {
        registrarError('Correo SMTP: ' . $e->getMessage());
        return false;
    } finally {
        @fclose($fp);
    }
}
