Configura el envío del correo de recuperación de contraseña (#11)
cristianfelipearango-cmyk
cristianfelipearango-cmyk
committed
yesterday
commit
c55342f
‎docs/diagramas/arquitectura.md‎
+1
Lines changed: 1 addition & 0 deletions


Original file line number	Diff line number	Diff line change
            HF[header.php · footer.php]
            ERR[aut_visualizar_error.php]
            SEG[csrf.php · validaciones.php<br/>recuperacion.php · correo.php]
            SMTP[smtp.php<br/>envío por SMTP o mail]
            PLAT[configuracion.php · mantenimiento.php<br/>parámetros de la plataforma]
        end
        ASSETS[assets/css/style.css]
‎docs/requerimientos/funcionales.md‎
+1
-1
Lines changed: 1 addition & 1 deletion


Original file line number	Diff line number	Diff line change
| RF-01 | Vitrina | El sistema debe permitir a cualquier visitante consultar el catálogo de locales y productos sin necesidad de registro. | Sí |
| RF-02 | Autenticación | El sistema debe permitir el registro de nuevos usuarios con nombre de usuario, correo electrónico y contraseña. | Sí |
| RF-03 | Autenticación | El sistema debe autenticar usuarios mediante correo electrónico y contraseña, asignando rol y permisos según el tipo de cuenta (visitante, comprador, vendedor, administrador). | Sí |
| RF-04 | Autenticación | El sistema debe permitir restablecer la contraseña mediante un correo de recuperación enviado al email registrado. | Sí: token de un solo uso que vence a los 60 min; el envío usa `mail()` y requiere correo configurado en el servidor |
| RF-04 | Autenticación | El sistema debe permitir restablecer la contraseña mediante un correo de recuperación enviado al email registrado. | Sí: token de un solo uso que vence a los 60 min; el envío es por SMTP (`includes/smtp.php`) con los datos de una cuenta de correo en `config.local.php` o en variables de entorno, o por `mail()` si el servidor lo tiene configurado. Si el correo no se puede enviar se avisa y el enlace no se muestra en pantalla |
| RF-05 | Locales | El sistema debe permitir al vendedor crear un local con nombre, categoría, descripción, número de local en la galería, teléfono/WhatsApp, correo de contacto y horario de atención. | Sí |
| RF-06 | Locales | El sistema debe permitir al vendedor modificar la información de su local en cualquier momento. | Sí |
| RF-07 | Locales | El sistema debe permitir al administrador o dueño desactivar (eliminar lógicamente) un local de la plataforma. | Sí |
‎features/autenticacion.feature‎
+14
Lines changed: 14 additions & 0 deletions
Original file line number	Diff line number	Diff line change
    Cuando ingreso mi correo con una contraseña incorrecta
    Entonces veo el mensaje "Correo o contraseña incorrectos."
    Y no se inicia sesión
  # Issue #11 (RF-04): envío del correo de recuperación de contraseña
  Escenario: Recuperar la contraseña con un correo registrado
    Dado que existe un usuario con el correo "usuario@ejemplo.com" y el servidor tiene el correo configurado
    Cuando solicito recuperar la contraseña con ese correo
    Entonces recibo un correo con el enlace de recuperación
    Y el enlace no se muestra en pantalla
  Escenario: El servidor no puede enviar el correo
    Dado que el servidor no tiene el correo configurado
    Cuando solicito recuperar la contraseña
    Entonces veo un mensaje que indica que no se pudo enviar el correo
    Y el enlace de recuperación no se muestra en pantalla
‎includes/config.local.example.php‎
+21
Lines changed: 21 additions & 0 deletions
Original file line number	Diff line number	Diff line change
// aparece en el panel de administración, y el aviso queda en logs/errores.log.
// define('CORREO_EQUIPO', 'equipo@tudominio.com');      // uno o varios, separados por coma
// define('CORREO_REMITENTE', 'no-responder@tudominio.com'); // opcional
// ------------------------------------------------------------
// Envío real de la recuperación de contraseña (HT #11). En tu equipo basta el
// modo desarrollo de arriba; esto es para probar el envío de verdad o para el
// servidor donde publiques el sitio. Con SMTP_HOST el sitio envía por SMTP; sin
// él usa mail(). CORREO_REMITENTE (arriba) es el remitente de estos correos y,
// si no lo defines, se usa SMTP_USUARIO.
// Ejemplo con Gmail (cuenta con verificación en dos pasos y una "contraseña de
// aplicación"; NO uses la contraseña normal de la cuenta):
//
// define('SMTP_HOST', 'smtp.gmail.com');
// define('SMTP_PORT', 587);
// define('SMTP_SEGURIDAD', 'tls');          // 'tls' (587), 'ssl' (465) o 'ninguna'
// define('SMTP_USUARIO', 'tu_cuenta@gmail.com');
// define('SMTP_CLAVE', 'tu-contraseña-de-aplicación');
//
// Con el modo desarrollo activo (MOSTRAR_ENLACE_RECUPERACION = true) no se envía
// correo: para probar el envío real, pon esa constante en false o bórrala.
// En un contenedor o hosting puedes usar variables de entorno con los mismos
// nombres (SMTP_HOST, SMTP_PORT, SMTP_SEGURIDAD, SMTP_USUARIO, SMTP_CLAVE), así la
// contraseña no vive en ningún archivo del proyecto.
‎includes/recuperacion.php‎
+21
-6
Lines changed: 21 additions & 6 deletions
Original file line number	Diff line number	Diff line change

const TOKEN_MINUTOS = 60; // cuánto dura un enlace

require_once __DIR__ . '/smtp.php'; // envío de correo por SMTP o mail() (HT #11)
function hashToken(string $token): string {
    return hash('sha256', $token);
}
    return $token;
}

/**
 * Deja vencidos los enlaces pendientes de un usuario. Se usa cuando el correo no
 * se pudo enviar: así no queda un enlace válido que nadie recibió.
 */
function anularTokensPendientes(PDO $db, int $iduser): void {
    $db->prepare(
        "UPDATE tokenrecuperacion SET fechaExpira = NOW()
         WHERE iduser = ? AND fechaUso IS NULL AND fechaExpira > NOW()"
    )->execute([$iduser]);
}
/**
 * Revisa un token. Devuelve ['estado' => ..., 'idToken' => ..., 'iduser' => ...]
 * con estado: 'valido' | 'usado' | 'vencido' | 'invalido'.

/**
 * Entrega el enlace. En desarrollo lo guarda en logs/recuperacion.log
 * (la pantalla lo muestra la página). En producción intenta enviarlo por correo.
 * (la pantalla lo muestra la página). En producción lo envía por correo.
 * Devuelve true si el enlace quedó entregado y false si el correo no se pudo enviar.
 */
function entregarEnlaceRecuperacion(string $email, string $enlace): void {
function entregarEnlaceRecuperacion(string $email, string $enlace): bool {
    if (modoDesarrollo()) {
        if (!is_dir(LOG_DIR)) @mkdir(LOG_DIR, 0775, true);
        $ht = LOG_DIR . '/.htaccess';
        if (is_dir(LOG_DIR) && !file_exists($ht)) @file_put_contents($ht, "Require all denied\n");
        $linea = '[' . date('Y-m-d H:i:s') . '] ' . $email . ' ' . $enlace . PHP_EOL;
        @file_put_contents(LOG_DIR . '/recuperacion.log', $linea, FILE_APPEND | LOCK_EX);
        return;
        return true;
    }

    $asunto = '=?UTF-8?B?' . base64_encode('Restablece tu contraseña de ' . nombreSitio()) . '?=';
    $cuerpo = "Recibimos una solicitud para restablecer tu contraseña.\r\n\r\n"
            . "Abre este enlace (dura " . TOKEN_MINUTOS . " minutos y solo sirve una vez):\r\n"
            . $enlace . "\r\n\r\n"
            . "Si no fuiste tú, ignora este mensaje: tu contraseña no cambia.\r\n";
    $cabeceras = "MIME-Version: 1.0\r\nContent-Type: text/plain; charset=UTF-8";
    if (!@mail($email, $asunto, $cuerpo, $cabeceras)) {
        registrarError('No se pudo enviar el correo de recuperación (mail() falló).');
    if (!enviarCorreo($email, $asunto, $cuerpo)) {
        registrarError('No se pudo enviar el correo de recuperación.');
        return false;
    }
    return true;
}
‎includes/smtp.php‎
+214
Lines changed: 214 additions & 0 deletions
Original file line number	Diff line number	Diff line change
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
‎usuario/olvidoContrasena.php‎
+16
-7
Lines changed: 16 additions & 7 deletions
Original file line number	Diff line number	Diff line change
// ============================================================
//  GALEZARZAL — Olvidé mi contraseña
//  Genera un enlace de un solo uso (vence en 60 minutos).
//  En desarrollo local el enlace se muestra en pantalla y se
//  guarda en logs/recuperacion.log (ver includes/config.php).
//  El enlace llega por correo y nunca se muestra en pantalla. Si el
//  correo no se pudo enviar, se avisa. Solo en desarrollo local el enlace
//  se muestra en pantalla y se guarda en logs/recuperacion.log (ver
//  includes/config.php). El envío se configura en includes/smtp.php.
// ============================================================
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/aut_visualizar_error.php';
            $st->execute([$email]);
            $iduser = $st->fetchColumn();

            // Mismo mensaje exista o no el correo, para no revelar quién tiene cuenta
            $mensaje = 'Si el correo está registrado, recibirás instrucciones en tu bandeja de entrada.';
            $tipo    = 'exito';
            if ($iduser) {
                $token = crearTokenRecuperacion($db, (int)$iduser);
                $url   = enlaceRecuperacion($token);
                entregarEnlaceRecuperacion($email, $url);
                if (enlaceEnPantalla()) $enlace = $url;
                if (entregarEnlaceRecuperacion($email, $url)) {
                    if (enlaceEnPantalla()) $enlace = $url;
                } else {
                    // El correo no salió: se vence el enlace y se avisa. Nunca se muestra el enlace.
                    anularTokensPendientes($db, (int)$iduser);
                    $mensaje = 'No se pudo enviar el correo de recuperación. Inténtalo de nuevo en unos minutos o avisa al administrador del sitio.';
                    $tipo    = 'error';
                }
            }
            // Mismo mensaje exista o no el correo, para no revelar quién tiene cuenta
            $mensaje = 'Si el correo está registrado, recibirás instrucciones en tu bandeja de entrada.';
            $tipo    = 'exito';
        } catch (PDOException $e) {
            registrarError('Recuperación de contraseña: ' . $e->getMessage());
            $mensaje = 'No pudimos procesar tu solicitud. Inténtalo de nuevo en unos minutos.';
‎README.md‎
+13
-1
Lines changed: 13 additions & 1 deletion


Original file line number	Diff line number	Diff line change

**Recuperar la contraseña en local.** Cuando alguien pide restablecer su contraseña, el sitio le manda un enlace por correo. En tu equipo no hay servidor de correo, así que ese correo no llegaría. Para trabajar sin él, copia `includes/config.local.example.php` como `includes/config.local.php` (Git ignora ese archivo, así que no se sube). Con eso el sitio no envía correo: el enlace aparece en pantalla, en un recuadro de "Modo desarrollo", y también queda en `logs/recuperacion.log`. El recuadro solo se muestra si abres el sitio desde el mismo equipo (`localhost`); si lo abres desde el celular u otro computador de la red, busca el enlace en ese archivo de log.

**Configurar el correo para que el envío de la recuperación sea real.** El sitio envía el correo de recuperación por SMTP si le das los datos de una cuenta de correo, por ejemplo una cuenta del equipo en Gmail. En Gmail la cuenta debe tener la verificación en dos pasos y necesitas crear una "contraseña de aplicación" (no sirve la contraseña normal). Pon estos datos en `includes/config.local.php` (copia `config.local.example.php`, que trae el ejemplo comentado), o como variables de entorno con los mismos nombres si publicas el sitio en un contenedor u hosting:
| Ajuste | Para qué sirve |
|---|---|
| `SMTP_HOST` | Servidor de correo, por ejemplo `smtp.gmail.com`. Si no existe, el sitio usa `mail()` de PHP |
| `SMTP_PORT` | `587` (por defecto) o `465` |
| `SMTP_SEGURIDAD` | `tls` (por defecto con el 587), `ssl` (con el 465) o `ninguna` (solo hacia el propio equipo) |
| `SMTP_USUARIO` y `SMTP_CLAVE` | Cuenta y contraseña con las que se inicia sesión |
| `CORREO_REMITENTE` y `CORREO_REMITENTE_NOMBRE` | Remitente que ve quien recibe el correo (por defecto, la cuenta SMTP y el nombre del sitio). `CORREO_REMITENTE` es el mismo ajuste de los mensajes de contacto |
Para probar el envío real en tu equipo, pon `MOSTRAR_ENLACE_RECUPERACION` en `false` o quita esa línea, porque con el modo desarrollo activo no se envía correo. Si el correo no se puede enviar (datos incorrectos, servidor caído o sin correo configurado), la persona ve un aviso de que no se pudo enviar, el enlace no se muestra en pantalla y queda vencido; el motivo técnico se guarda en `logs/errores.log`, sin contraseñas. Un detalle para el hosting: muchos hostings gratuitos desactivan `mail()` o bloquean los puertos SMTP, por eso conviene comprobarlo al elegirlo (issue #24). Por ahora, el SMTP solo se usa en la recuperación de contraseña; los mensajes de contacto siguen saliendo con `mail()`.
Sin ese archivo, el sitio se comporta como en producción: nunca muestra el enlace en pantalla. Eso es a propósito, porque si se mostrara en un sitio publicado cualquiera podría cambiarle la contraseña a otra persona. No copies `config.local.php` al servidor donde publiques el sitio.

**Correo de los mensajes de contacto.** Cada mensaje del formulario de Ayuda y Contacto se guarda en la base y, además, se manda por correo al equipo. Para activarlo define `CORREO_EQUIPO` (uno o varios correos separados por coma) y, si tu hosting lo exige, `CORREO_REMITENTE` (una dirección de tu dominio desde la que sale el correo). Se pueden poner como variables de entorno del servidor o en `includes/config.local.php` (hay un ejemplo comentado en `includes/config.local.example.php`). Si no están definidas, o si el servidor no puede enviar el correo, el mensaje se guarda igual y aparece en el panel de administración, y el aviso queda en `logs/errores.log`; quien escribió no ve ningún error. En el servidor publicado, si usas `config.local.php`, que tenga solo esas líneas y nunca `MOSTRAR_ENLACE_RECUPERACION`.

Es una vitrina y un canal de contacto, no una tienda con pagos en línea. La compra se acuerda directamente entre comprador y vendedor por WhatsApp y no hay pasarela de pago.

La recuperación de contraseña funciona con enlaces de un solo uso que vencen a los 60 minutos, pero el envío del correo todavía depende de que el servidor tenga correo configurado: el código usa `mail()`, que en XAMPP no viene listo. Por seguridad, el enlace nunca se muestra en pantalla a menos que actives el modo desarrollo en tu propio equipo (ver "Recuperar la contraseña en local" más arriba).
La recuperación de contraseña funciona con enlaces de un solo uso que vencen a los 60 minutos, pero el envío del correo depende de que el servidor tenga correo configurado: por SMTP con los ajustes de `includes/config.local.php` (o variables de entorno), o con `mail()`, que en XAMPP no viene listo (ver "Configurar el correo para que el envío de la recuperación sea real" más arriba). Por seguridad, el enlace nunca se muestra en pantalla a menos que actives el modo desarrollo en tu propio equipo (ver "Recuperar la contraseña en local" más arriba).

La guía de instalación está pensada para XAMPP en Windows, pero el código también funciona en servidores Linux, que sí distinguen mayúsculas de minúsculas. El módulo de locales vive en la carpeta `local/` y todas las consultas usan los nombres de tabla en minúscula, tal como están en `database/galezarzalBD.sql`, así que no hace falta configurar `lower_case_table_names` en MySQL. Al desplegar en Linux, la carpeta del proyecto debe llamarse `galezarzal` en minúscula, porque los enlaces internos siguen apuntando a `/galezarzal/` (Issue #10). Si agregas código nuevo, escribe siempre en minúscula los nombres de carpeta y de tabla.
# Galezarzal

Galezarzal es un marketplace web para los comerciantes de la Galería de Zarzal, Valle del Cauca. Cada local puede publicar su catálogo en línea, y cualquier persona puede entrar al sitio desde el navegador, ver los productos, comparar estilos, tallas y precios, y escribirle al vendedor por WhatsApp sin pasar por intermediarios.

## Qué se puede hacer

Hay tres tipos de usuario y cada uno tiene su propio recorrido.

**Compradores.** Pueden explorar los locales y filtrarlos por categoría, entrar al perfil de cada uno para ver su catálogo, guardar sus favoritos y escribirle al vendedor por WhatsApp con un clic. También reciben notificaciones, pueden leer las preguntas frecuentes y mandar un mensaje desde la sección de ayuda.

**Vendedores.** Registran su local con un asistente de cuatro pasos (datos del local, productos, horarios y confirmación) y después lo manejan desde su panel: información, catálogo con precio y talla, horarios de atención y visibilidad. Un local nuevo queda pendiente y solo se hace público cuando un administrador lo aprueba.

**Administradores.** Gestionan usuarios y moderan locales y productos. La pantalla de configuración general (nombre del sitio, modo mantenimiento, registro abierto o cerrado y aprobación manual) guarda los cambios en la base de datos. Por ahora solo se aplica el registro abierto o cerrado; el resto está pendiente en el Issue #41.

Todos pueden crear y editar su cuenta, cambiar la contraseña o borrar la cuenta. Las preferencias de notificaciones y de privacidad se guardan y se mantienen al volver a iniciar sesión. Los locales se clasifican en seis categorías: Ropa Mujer, Ropa Hombre, Ropa Niños, Accesorios, Calzado y Ropa Deportiva.

## Con qué está hecho

PHP 8 con PDO para hablar con la base de datos, MySQL o MariaDB para guardar los datos, y HTML y CSS escritos a mano. Los colores del sitio están en `assets/css/style.css`: rojo `#E94560`, azul oscuro `#1A1A2E` y crema `#F1FAEE`. Para desarrollar usamos XAMPP o Docker Compose.

## Antes de instalar: una advertencia sobre PHP

El código usa `match` y `str_contains`, así que necesitas **PHP 8.0 o superior**. Pero no te quedes con cualquier XAMPP que encuentres, porque los instaladores viejos traen una vulnerabilidad seria.

En junio de 2024 se publicó el **CVE-2024-4577**, una falla crítica (9.8 sobre 10) en PHP para Windows. Permite que alguien de afuera ejecute código en el servidor aprovechando cómo PHP-CGI interpreta ciertos caracteres. Quedó corregida en PHP 8.1.29, 8.2.20 y 8.3.8, así que cualquier versión anterior a esas, o una rama que ya no recibe soporte, sigue expuesta.

Lo que lo hace delicado para nosotros es que XAMPP en Windows deja por defecto la carpeta de PHP accesible desde Apache mediante un `ScriptAlias`, y con eso basta para que sea explotable aunque no uses CGI a propósito. El caso documentado es con Windows configurado en chino o japonés, pero no conviene apostar a que el idioma te protege. Los ataques empezaron a los pocos días de publicarse la falla, incluso con ransomware.

Esto significa que los XAMPP 8.2.12, 8.1.25 y 8.0.30 de noviembre de 2023 (los que se recomendaban hasta hace poco) traen un PHP anterior al parche. Si ya los tienes instalados, actualiza.

Lo que recomendamos:

- Instalar la versión más reciente de XAMPP desde [apachefriends.org](https://www.apachefriends.org/download.html). Al momento de escribir esto es la 8.5.12 (octubre de 2026), que trae PHP 8.5.12 y MariaDB 10.11.16.
- Si no necesitas CGI, abrir `C:\xampp\apache\conf\extra\httpd-xampp.conf` y comentar con `#` la línea `ScriptAlias /php-cgi/ "C:/xampp/php/"`. Reinicia Apache después.
- No publicar este proyecto en internet tal como está. La configuración de la base de datos (usuario `root` sin contraseña) es solo para trabajar en local.

### ¿Se puede usar un MariaDB más nuevo con el PHP reciente?

Sí. PHP no está atado a una versión de MariaDB: el proyecto solo usa PDO con el driver de MySQL, y el script SQL funciona desde MariaDB 10.4 en adelante. XAMPP trae MariaDB 10.11, que sigue siendo una versión soportada, así que para trabajar no necesitas cambiar nada.

Si de todas formas quieres una versión más reciente (por ejemplo una 11.x), se puede cambiar el motor dentro de XAMPP a mano. No es un procedimiento oficial de XAMPP, así que haz el respaldo antes y guarda la carpeta vieja por si toca volver atrás:

1. Abre el Shell del panel de XAMPP y saca un respaldo de todo: `mysqldump -u root -p --all-databases > respaldo.sql`
2. Detén Apache y MySQL desde el panel y ciérralo.
3. Renombra `C:\xampp\mysql` a `mysql-old`.
4. Descarga el ZIP de Windows de la versión que quieras desde [mariadb.org/download](https://mariadb.org/download/) y descomprímelo para que su contenido quede en `C:\xampp\mysql`.
5. Copia desde `mysql-old` las carpetas `data`, `backup` y `scripts`, el archivo `bin\my.ini` y los `.bat` de la raíz (`mysql_installservice.bat` y `mysql_uninstallservice.bat`).
6. Inicia MySQL desde el panel y ejecuta el actualizador de tablas: `mysql_upgrade.exe` (en las versiones nuevas se llama `mariadb-upgrade.exe`).
7. Reinicia MySQL y revisa que la aplicación abra bien.

Si algo sale mal, borra la carpeta `mysql` nueva, devuelve `mysql-old` a su nombre y listo.

## Instalación en local con XAMPP

Necesitas XAMPP para Windows (con PHP 8.0 o superior, pero ya viste que conviene la última) y Git, este último solo si quieres clonar el repositorio.

**1. Copia el proyecto.** Tiene que quedar en `C:\xampp\htdocs\galezarzal`, con ese nombre exacto y en minúsculas, porque los enlaces internos apuntan a `/galezarzal/`.

```
git clone https://github.com/mihzuk/Galezarzal.git C:\xampp\htdocs\galezarzal
```

Si prefieres bajar el ZIP desde GitHub, descomprímelo y cambia el nombre de la carpeta `Galezarzal-main` por `galezarzal`. Revisa que `index.php` quede directamente dentro de esa carpeta y no en una subcarpeta.

**2. Enciende los servicios.** Abre el Panel de Control de XAMPP e inicia Apache y MySQL.

**3. Crea la base de datos.** Entra a phpMyAdmin (http://localhost/phpmyadmin) y:

1. Haz clic en Nueva, ponle de nombre `galezarzal`, elige el cotejamiento `utf8mb4_unicode_ci` y presiona Crear.
2. Selecciona la base `galezarzal` en el panel de la izquierda. El script no trae `CREATE DATABASE` ni `USE`, así que si lo importas sin tenerla seleccionada te sale el error "No database selected".
3. Ve a la pestaña Importar, elige `database/galezarzalBD.sql` y presiona Importar.
4. Al terminar deberías ver 17 tablas.

Si ya tenías la base importada antes de que existieran las tablas de mensajes de contacto y de recuperación de contraseña, no hace falta rehacerla: ejecuta `database/mensajeContacto.sql` y `database/tokenRecuperacion.sql` en la pestaña SQL, con la base seleccionada.

Si necesitas repetir la importación, borra primero la base desde Operaciones y vuélvela a crear. El script no usa `IF NOT EXISTS`, entonces sobre tablas que ya existen falla con "Table already exists".

**4. Abre el sitio.** Entra a http://localhost/galezarzal/ y deberías ver la página de inicio con el local de ejemplo "el niche store".

La conexión a la base de datos se configura en `includes/config.php`: usuario `root`, sin contraseña y base `galezarzal`.
## Instalación en local con Docker

Como alternativa a XAMPP, Galezarzal cuenta con un entorno de desarrollo basado en Docker Compose (Issue #43). Esto permite trabajar con la misma configuración en Windows, Linux y macOS.

### Requisitos

- Docker Desktop o Docker Engine con Docker Compose.
- Git.
- Tener Docker iniciado antes de ejecutar los comandos.

### Instalación

1. Clona el repositorio:

   ```bash
   git clone https://github.com/mihzuk/Galezarzal.git
   cd Galezarzal
   ```

2. Crea el archivo de variables de entorno:

   ```bash
   cp .env.example .env
   ```

3. Abre el archivo `.env` y configura las contraseñas de la base de datos. Este archivo está excluido de Git y no debe subirse al repositorio.

4. Construye e inicia los contenedores:

   ```bash
   docker compose up -d --build
   ```

5. Comprueba el estado de los servicios:

   ```bash
   docker compose ps
   ```

6. Una vez integrada la configuración de credenciales del Issue #18, abre la aplicación en:

   http://localhost:8080/galezarzal/

### Servicios

| Servicio | Tecnología | Puerto |
|---|---|---|
| web | PHP 8.3 + Apache + PDO MySQL | 8080 |
| db | MySQL 8.4 | 3306 interno |

La base de datos se inicializa automáticamente mediante el archivo `database/galezarzalBD.sql` cuando se crea el volumen por primera vez.

Los datos se almacenan en el volumen `mysql_data`, lo que permite conservarlos al detener o recrear los contenedores.

### Comandos útiles

```bash
# Iniciar los contenedores
docker compose up -d

# Consultar el estado
docker compose ps

# Consultar los registros
docker compose logs

# Reconstruir después de modificar el código
docker compose up -d --build

# Detener y eliminar los contenedores
docker compose down
```

No utilices `docker compose down -v` si quieres conservar la información de la base de datos, ya que este comando elimina el volumen.

### Compatibilidad con XAMPP

Docker es una alternativa de desarrollo y no reemplaza XAMPP. Ambos entornos pueden funcionar de manera independiente y utilizan bases de datos separadas.

### Estado de integración

Se verificó la construcción de las imágenes, la importación de las 17 tablas, los datos de ejemplo, la conexión directa entre PHP y MySQL y la conservación de los datos existentes al recrear los contenedores.

La integración de las credenciales en la aplicación depende del Issue #18. Hasta que se complete, quedan pendientes las pruebas del catálogo y del registro de usuarios desde Docker.

## Primeros pasos en la aplicación

Los datos de `galezarzalBD.sql` son inventados. El usuario de ejemplo (`demo@example.com`, con rol de administrador) no tiene una contraseña válida, así que no sirve para iniciar sesión. Lo primero es crear tu propia cuenta.

Para eso entra a http://localhost/galezarzal/usuario/ingresarCuenta.php, abre la pestaña Crear cuenta y llena el formulario (la contraseña debe tener mínimo seis caracteres). La cuenta nace con rol de comprador.

Si quieres publicar un local, inicia sesión y usa el botón "tengo un local" en la página de inicio. Al terminar el asistente pasas a ser vendedor, pero tu local queda pendiente y no aparece en el inicio ni en el buscador hasta que un administrador lo apruebe.

**Recuperar la contraseña en local.** Cuando alguien pide restablecer su contraseña, el sitio le manda un enlace por correo. En tu equipo no hay servidor de correo, así que ese correo no llegaría. Para trabajar sin él, copia `includes/config.local.example.php` como `includes/config.local.php` (Git ignora ese archivo, así que no se sube). Con eso el sitio no envía correo: el enlace aparece en pantalla, en un recuadro de "Modo desarrollo", y también queda en `logs/recuperacion.log`. El recuadro solo se muestra si abres el sitio desde el mismo equipo (`localhost`); si lo abres desde el celular u otro computador de la red, busca el enlace en ese archivo de log.

**Configurar el correo para que el envío de la recuperación sea real.** El sitio envía el correo de recuperación por SMTP si le das los datos de una cuenta de correo, por ejemplo una cuenta del equipo en Gmail. En Gmail la cuenta debe tener la verificación en dos pasos y necesitas crear una "contraseña de aplicación" (no sirve la contraseña normal). Pon estos datos en `includes/config.local.php` (copia `config.local.example.php`, que trae el ejemplo comentado), o como variables de entorno con los mismos nombres si publicas el sitio en un contenedor u hosting:

| Ajuste | Para qué sirve |
|---|---|
| `SMTP_HOST` | Servidor de correo, por ejemplo `smtp.gmail.com`. Si no existe, el sitio usa `mail()` de PHP |
| `SMTP_PORT` | `587` (por defecto) o `465` |
| `SMTP_SEGURIDAD` | `tls` (por defecto con el 587), `ssl` (con el 465) o `ninguna` (solo hacia el propio equipo) |
| `SMTP_USUARIO` y `SMTP_CLAVE` | Cuenta y contraseña con las que se inicia sesión |
| `CORREO_REMITENTE` y `CORREO_REMITENTE_NOMBRE` | Remitente que ve quien recibe el correo (por defecto, la cuenta SMTP y el nombre del sitio). `CORREO_REMITENTE` es el mismo ajuste de los mensajes de contacto |

Para probar el envío real en tu equipo, pon `MOSTRAR_ENLACE_RECUPERACION` en `false` o quita esa línea, porque con el modo desarrollo activo no se envía correo. Si el correo no se puede enviar (datos incorrectos, servidor caído o sin correo configurado), la persona ve un aviso de que no se pudo enviar, el enlace no se muestra en pantalla y queda vencido; el motivo técnico se guarda en `logs/errores.log`, sin contraseñas. Un detalle para el hosting: muchos hostings gratuitos desactivan `mail()` o bloquean los puertos SMTP, por eso conviene comprobarlo al elegirlo (issue #24). Por ahora, el SMTP solo se usa en la recuperación
**Decisión sobre el mensaje cuando el envío falla.** Si el correo no se puede enviar, solo quien escribe un correo registrado ve "No se pudo enviar el correo de recuperación", porque el envío solo se intenta cuando la cuenta existe. Quien escribe un correo no registrado ve el mensaje de éxito de siempre. Con el envío funcionando no hay ninguna diferencia. Se aceptó así para cumplir el escenario 2 del Issue #11 (avisar cuando el correo no sale). Pendiente de confirmar con el PO. Además, con un servidor SMTP que no responde, la solicitud de un correo registrado puede tardar hasta 8 segundos (tiempo máximo de conexión y de espera en `includes/smtp.php`). de contraseña; los mensajes de contacto siguen saliendo con `mail()`.

Sin ese archivo, el sitio se comporta como en producción: nunca muestra el enlace en pantalla. Eso es a propósito, porque si se mostrara en un sitio publicado cualquiera podría cambiarle la contraseña a otra persona. No copies `config.local.php` al servidor donde publiques el sitio.

**Correo de los mensajes de contacto.** Cada mensaje del formulario de Ayuda y Contacto se guarda en la base y, además, se manda por correo al equipo. Para activarlo define `CORREO_EQUIPO` (uno o varios correos separados por coma) y, si tu hosting lo exige, `CORREO_REMITENTE` (una dirección de tu dominio desde la que sale el correo). Se pueden poner como variables de entorno del servidor o en `includes/config.local.php` (hay un ejemplo comentado en `includes/config.local.example.php`). Si no están definidas, o si el servidor no puede enviar el correo, el mensaje se guarda igual y aparece en el panel de administración, y el aviso queda en `logs/errores.log`; quien escribió no ve ningún error. En el servidor publicado, si usas `config.local.php`, que tenga solo esas líneas y nunca `MOSTRAR_ENLACE_RECUPERACION`.

Y como el proyecto no trae ningún administrador utilizable, ese rol hay que asignarlo a mano. En phpMyAdmin, con la base `galezarzal` seleccionada, ve a la pestaña SQL y ejecuta esto cambiando el correo por el de tu cuenta:

```sql
UPDATE usuario SET idRol = 3 WHERE email = 'tu_correo@ejemplo.com';
```

Cierra sesión y vuelve a entrar, porque el rol se guarda en la sesión al ingresar. En el menú de Configuración te va a aparecer la parte de administración, y los locales pendientes los apruebas en http://localhost/galezarzal/admin/moderarLocales.php.

## Si algo falla

- **Error 404, página sin estilos o enlaces rotos.** Casi seguro la carpeta no se llama `galezarzal` o quedó metida dentro de otra. Repasa el primer paso de la instalación.
- **"Error de conexión a la base de datos".** Revisa que MySQL esté encendido, que la base se llame `galezarzal` y que `root` no tenga contraseña. Si la tiene, cambia `DB_PASS` en `includes/config.php`.
- **Errores de sintaxis o `Call to undefined function str_contains()`.** Tu PHP es anterior a la 8.0. Actualiza XAMPP.
- **Apache no arranca porque el puerto 80 está ocupado.** Cambia el puerto en la configuración de XAMPP y entra con `http://localhost:PUERTO/galezarzal/`.

## Lo que Galezarzal no hace (por ahora)

Es una vitrina y un canal de contacto, no una tienda con pagos en línea. La compra se acuerda directamente entre comprador y vendedor por WhatsApp y no hay pasarela de pago.

La recuperación de contraseña funciona con enlaces de un solo uso que vencen a los 60 minutos, pero el envío del correo depende de que el servidor tenga correo configurado: por SMTP con los ajustes de `includes/config.local.php` (o variables de entorno), o con `mail()`, que en XAMPP no viene listo (ver "Configurar el correo para que el envío de la recuperación sea real" más arriba). Por seguridad, el enlace nunca se muestra en pantalla a menos que actives el modo desarrollo en tu propio equipo (ver "Recuperar la contraseña en local" más arriba).

La guía de instalación incluye XAMPP en Windows y Docker Compose, pero el código también funciona en servidores Linux, que sí distinguen mayúsculas de minúsculas. El módulo de locales vive en la carpeta `local/` y todas las consultas usan los nombres de tabla en minúscula, tal como están en `database/galezarzalBD.sql`, así que no hace falta configurar `lower_case_table_names` en MySQL. Al desplegar en Linux, la carpeta del proyecto debe llamarse `galezarzal` en minúscula, porque los enlaces internos siguen apuntando a `/galezarzal/` (Issue #10). Si agregas código nuevo, escribe siempre en minúscula los nombres de carpeta y de tabla.

## Equipo

Mantenemos el repositorio [@josdaver3000](https://github.com/josdaver3000), [@Johan-v-p](https://github.com/Johan-v-p), [@mihzuk](https://github.com/mihzuk) y [@lizarazo2005](https://github.com/lizarazo2005). Lo desarrolla el grupo de Agora: Cristian, que es el Scrum Master fijo, Divis y Juan Cardona. Los roles y los cambios en el equipo están en [`docs/scrum.md`](docs/scrum.md#roles).

## Proceso Scrum y documentación

Trabajamos con Scrum y un tablero Kanban en GitHub Projects. Cómo lo hacemos, con las ceremonias y las plantillas, está en la carpeta `docs/`:

- [Cómo trabajamos con Scrum](docs/scrum.md): roles y cambios en el equipo, Product Goal, duración del sprint, ceremonias, tablero, Git y cambios de diseño.
- [Definition of Done](docs/DoD.md) y [Definition of Ready](docs/DoR.md).
- [Requerimientos funcionales](docs/requerimientos/funcionales.md) y [no funcionales](docs/requerimientos/no-funcionales.md).
- Diagramas en código (Mermaid): [entidad-relación](docs/diagramas/er.md), [clases](docs/diagramas/clases.md), [casos de uso](docs/diagramas/casos-de-uso.md), [secuencia](docs/diagramas/secuencia.md) y [arquitectura](docs/diagramas/arquitectura.md).
- [Colaboración entre grupos](docs/colaboracion-entre-grupos.md) y [backlog inicial](docs/backlog-inicial.md).
- Los documentos originales de Desarrollo de Software I (ICONIX): el [documento funcional](docs/legacy/Documento_Funcional_ICONIX.pdf) y los [diagramas en draw.io](docs/legacy/Diagramas_ICONIX.drawio).
- Los criterios de aceptación en Gherkin están en [`features/`](features/) y las actas en [`docs/actas/`](docs/actas/).

Si un cambio modifica el diseño original, hay que actualizar los diagramas y la documentación en el mismo pull request.

## Cómo contribuir

La rama `main` está protegida: nadie sube cambios directo, todo entra con un pull request aprobado. Trabajamos con una tarea por Issue, una rama y un pull request.

1. Elige un Issue del Backlog en el Project, asígnatelo y muévelo a In progress.
2. Actualiza `main` y crea una rama nueva para la tarea:

   ```
   git switch main
   git pull origin main
   git switch -c feature/nombre-de-la-tarea
   ```

   Usamos `feature/...` para funciones nuevas, `fix/...` para correcciones y `docs/...` para documentación.

3. Haz los cambios, pruébalos en XAMPP y sube la rama:

   ```
   git add .
   git commit -m "feat: descripción breve del cambio"
   git push -u origin feature/nombre-de-la-tarea
   ```

4. Abre un pull request hacia `main` y escribe en la descripción `Closes #número-del-issue`.
5. Espera la revisión. Si piden cambios, corrígelos en la misma rama y haz push; el pull request se actualiza solo.
6. Cuando lo aprueben y se una a `main`, borra la rama. Para la siguiente tarea crea una rama nueva (no reutilices la anterior) y siempre después de hacer `git pull` en `main`.
