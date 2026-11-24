<?php
// ============================================================
//  GALEZARZAL — Correo de los mensajes de contacto (RF-24)
//  Cada mensaje del formulario de Ayuda y Contacto se guarda en
//  la base y, además, se manda por correo al equipo. Si el correo
//  falla, el mensaje ya quedó guardado y el error va a
//  logs/errores.log; la persona que escribió no ve ningún error.
//  Destinatarios: CORREO_EQUIPO (includes/config.local.php o la
//  variable de entorno CORREO_EQUIPO). Ver README.
// ============================================================
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/configuracion.php'; // nombre del sitio

/** Direcciones válidas del equipo (CORREO_EQUIPO admite varias separadas por coma). */
function correosEquipo(): array {
    $lista = [];
    foreach (explode(',', (string)CORREO_EQUIPO) as $correo) {
        $correo = trim($correo);
        if ($correo !== '' && filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $lista[] = $correo;
        }
    }
    return $lista;
}

/**
 * Codifica un texto para una cabecera de correo (RFC 2047): admite tildes y,
 * al quitar los saltos de línea, nadie puede colar cabeceras extra (por
 * ejemplo un "Bcc:") escribiendo un salto de línea en el asunto o el nombre.
 */
function cabeceraCodificada(string $texto): string {
    $texto = trim(preg_replace('/[\x00-\x1F\x7F]+/', ' ', $texto));
    if ($texto === '') return '';
    $trozos = [];
    $actual = '';
    foreach (preg_split('//u', $texto, -1, PREG_SPLIT_NO_EMPTY) ?: [] as $caracter) {
        if (strlen($actual . $caracter) > 30) { // cada palabra codificada queda bajo 75 caracteres
            $trozos[] = $actual;
            $actual   = '';
        }
        $actual .= $caracter;
    }
    if ($actual !== '') $trozos[] = $actual;
    return implode(' ', array_map(fn($t) => '=?UTF-8?B?' . base64_encode($t) . '?=', $trozos));
}

/**
 * Manda por correo al equipo un mensaje de contacto ya guardado.
 * $m: idMensaje, iduser (o null), nombre, email, asunto (o null), mensaje.
 * Devuelve true si el servidor aceptó el correo. Nunca lanza excepciones.
 */
function enviarCorreoContacto(array $m): bool {
    $idMensaje = (int)($m['idMensaje'] ?? 0);
    try {
        $para = correosEquipo();
        if (!$para) {
            registrarError("Contacto: el mensaje #$idMensaje se guardó, pero no se envió el correo porque CORREO_EQUIPO no está configurado o no tiene un correo válido.");
            return false;
        }

        $sitio   = nombreSitio();
        $nombre  = trim(preg_replace('/[\x00-\x1F\x7F]+/', ' ', (string)($m['nombre'] ?? '')));
        $email   = trim((string)($m['email'] ?? ''));
        $asunto  = trim(preg_replace('/[\x00-\x1F\x7F]+/', ' ', (string)($m['asunto'] ?? '')));
        $mensaje = str_replace(["\r\n", "\r"], "\n", (string)($m['mensaje'] ?? ''));
        $quien   = !empty($m['iduser'])
            ? 'Usuario registrado (id ' . (int)$m['iduser'] . ')'
            : 'Visitante sin cuenta';

        $cuerpo = "Nuevo mensaje de contacto en $sitio\n\n"
                . "De: $nombre <$email>\n"
                . "$quien\n"
                . 'Asunto: ' . ($asunto !== '' ? $asunto : '(sin asunto)') . "\n\n"
                . "Mensaje:\n$mensaje\n\n"
                . 'Respóndele a ese correo, o revisa el panel: ' . APP_URL . "/admin/mensajesContacto.php\n";
        $cuerpo = str_replace("\n", "\r\n", $cuerpo);

        $cabeceras = ['MIME-Version: 1.0', 'Content-Type: text/plain; charset=UTF-8', 'Content-Transfer-Encoding: base64'];
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) { // para responder con un solo clic
            $cabeceras[] = 'Reply-To: ' . ($nombre !== '' ? cabeceraCodificada($nombre) . ' ' : '') . "<$email>";
        }
        if (filter_var(CORREO_REMITENTE, FILTER_VALIDATE_EMAIL)) {
            $cabeceras[] = 'From: ' . cabeceraCodificada($sitio) . ' <' . CORREO_REMITENTE . '>';
        }

        $titulo = 'Contacto: ' . ($asunto !== '' ? $asunto : 'sin asunto');
        $enviado = @mail(
            implode(', ', $para),
            cabeceraCodificada($titulo),
            chunk_split(base64_encode($cuerpo)),
            implode("\r\n", $cabeceras)
        );
        if (!$enviado) {
            registrarError("Contacto: el mensaje #$idMensaje se guardó, pero mail() no pudo enviar el correo al equipo.");
            return false;
        }
        return true;
    } catch (Throwable $e) {
        registrarError("Contacto: el mensaje #$idMensaje se guardó, pero falló el envío del correo: " . $e->getMessage());
        return false;
    }
}
