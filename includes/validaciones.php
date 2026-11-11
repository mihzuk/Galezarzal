<?php
// ============================================================
//  GALEZARZAL — Validaciones compartidas (lado servidor)
//  Cada función devuelve '' si todo está bien, o el mensaje de
//  error para mostrarle al usuario. Los límites de largo
//  coinciden con las columnas de la base de datos.
// ============================================================

const SECCIONES_PRODUCTO = ['Remates', 'Novedades', 'Ofertas', 'Destacados'];
const DIAS_SEMANA        = ['LUN', 'MAR', 'MIE', 'JUE', 'VIE', 'SAB', 'DOM'];

/** Cuenta caracteres (no bytes), para que tildes y ñ cuenten como 1. */
function largoTexto(string $t): int {
    return mb_strlen($t, 'UTF-8');
}

/** Devuelve el primer mensaje de error no vacío de una lista ('' si no hay). */
function primerError(array $errores): string {
    foreach ($errores as $e) {
        if ($e !== '') return $e;
    }
    return '';
}

/** Texto con largo mínimo y máximo. Si $obligatorio es false, se permite vacío. */
function validarTexto(string $valor, string $campo, int $min, int $max, bool $obligatorio = true): string {
    $valor = trim($valor);
    if ($valor === '') {
        return $obligatorio ? "El campo «$campo» es obligatorio." : '';
    }
    $n = largoTexto($valor);
    if ($n < $min) return "«$campo» debe tener al menos $min caracteres.";
    if ($n > $max) return "«$campo» no puede tener más de $max caracteres.";
    return '';
}

/** Nombres y apellidos: 2 a 50 caracteres, solo letras, espacios, apóstrofo, punto y guion. */
function validarNombrePersona(string $valor, string $campo): string {
    $err = validarTexto($valor, $campo, 2, 50);
    if ($err !== '') return $err;
    if (!preg_match('/^\p{L}[\p{L} \'.\-]*$/u', trim($valor))) {
        return "«$campo» solo puede tener letras y espacios.";
    }
    return '';
}

/** Nombre de usuario: 3 a 30 caracteres, letras, números, punto y guion bajo. */
function validarUsuario(string $valor): string {
    $err = validarTexto($valor, 'Usuario', 3, 30);
    if ($err !== '') return $err;
    if (!preg_match('/^[A-Za-z0-9._]+$/', trim($valor))) {
        return '«Usuario» solo puede tener letras, números, punto y guion bajo (sin espacios).';
    }
    return '';
}

/** Correo: obligatorio, máximo 100 caracteres y con formato válido. */
function validarEmail(string $valor): string {
    $valor = trim($valor);
    if ($valor === '') return 'El correo es obligatorio.';
    if (largoTexto($valor) > 100) return 'El correo no puede tener más de 100 caracteres.';
    if (!filter_var($valor, FILTER_VALIDATE_EMAIL)) return 'El correo no tiene un formato válido.';
    return '';
}

/**
 * Normaliza un celular colombiano. Acepta 3001234567, 300 123 4567,
 * 300-123-4567 y +57 300 123 4567. Devuelve los 10 dígitos, o '' si no es válido.
 */
function normalizarCelular(string $valor): string {
    $solo = preg_replace('/[\s\-().]/', '', trim($valor));
    return preg_match('/^(?:\+?57)?(3\d{9})$/', $solo, $m) ? $m[1] : '';
}

/** WhatsApp para wa.me: 57 + 10 dígitos, sin "+" ni espacios (ej: 573001234567). */
function formatoWhatsapp(string $valor): string {
    $cel = normalizarCelular($valor);
    return $cel === '' ? '' : '57' . $cel;
}

/** Teléfono: si es obligatorio no puede ir vacío; si lo escriben, debe ser válido. */
function validarCelular(string $valor, bool $obligatorio): string {
    if (trim($valor) === '') {
        return $obligatorio ? 'El teléfono es obligatorio.' : '';
    }
    if (normalizarCelular($valor) === '') {
        return 'El teléfono debe ser un celular colombiano de 10 dígitos que empiece por 3 (ej: 300 123 4567).';
    }
    return '';
}

/** Contraseña: 6 a 72 caracteres (bcrypt solo usa los primeros 72 bytes) y debe coincidir. */
function validarPassword(string $pass, string $confirmar): string {
    if ($pass === '') return 'La contraseña es obligatoria.';
    if (strlen($pass) < 6) return 'La contraseña debe tener al menos 6 caracteres.';
    if (strlen($pass) > 72) return 'La contraseña no puede tener más de 72 caracteres.';
    if ($pass !== $confirmar) return 'Las contraseñas no coinciden.';
    return '';
}

/**
 * Convierte el texto de un precio en número, o null si no es válido.
 * Acepta 45000, 45.000 (miles con punto) y 45000,50 (decimales con coma o punto).
 */
function parsearPrecio(string $valor): ?float {
    $v = str_replace(['$', ' '], '', trim($valor));
    if (preg_match('/^\d{1,3}(\.\d{3})+$/', $v)) {
        $v = str_replace('.', '', $v);            // 45.000 -> 45000
    } elseif (preg_match('/^\d+([.,]\d{1,2})?$/', $v)) {
        $v = str_replace(',', '.', $v);           // 45000,50 -> 45000.50
    } else {
        return null;
    }
    return (float)$v;
}

/** Precio: obligatorio, número válido, mayor que 0 y que quepa en decimal(10,2). */
function validarPrecio(string $valor): string {
    $valor = trim($valor);
    if ($valor === '') return 'El precio es obligatorio.';
    if (str_starts_with($valor, '-')) return 'El precio no puede ser negativo.';
    $precio = parsearPrecio($valor);
    if ($precio === null) return 'El precio debe ser un número válido (ej: 45000).';
    if ($precio <= 0) return 'El precio debe ser mayor que 0.';
    if ($precio > 99999999) return 'El precio no puede superar los $99.999.999.';
    return '';
}

/** Convierte "url1, url2" en una lista de enlaces sin vacíos. */
function listaFotos(string $raw): array {
    return array_values(array_filter(array_map('trim', explode(',', $raw))));
}

/** Cada foto debe ser un enlace http o https de máximo 500 caracteres. */
function validarFotos(string $raw): string {
    foreach (listaFotos($raw) as $url) {
        if (largoTexto($url) > 500
            || !filter_var($url, FILTER_VALIDATE_URL)
            || !preg_match('#^https?://#i', $url)) {
            return 'Cada foto debe ser un enlace válido que empiece por http:// o https:// (máx. 500 caracteres).';
        }
    }
    return '';
}

/** La categoría elegida tiene que existir en la lista que viene de la BD. */
function validarCategoria(int $id, array $categorias): string {
    foreach ($categorias as $c) {
        if ((int)$c['idcategoria'] === $id) return '';
    }
    return 'Selecciona una categoría válida.';
}

/** Deja solo los días permitidos (LUN..DOM) y los une con comas: "LUN,MAR". */
function diasValidos(array $post): string {
    $marcados = array_keys(array_filter($post));
    return implode(',', array_values(array_intersect(DIAS_SEMANA, $marcados)));
}

/** Horario: horas con formato HH:MM, apertura antes del cierre y descanso de máx. 50. */
function validarHorario(string $apertura, string $cierre, string $descanso): string {
    $hora = '/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/';
    if (!preg_match($hora, $apertura) || !preg_match($hora, $cierre)) {
        return 'Las horas de apertura y cierre no tienen un formato válido.';
    }
    if (substr($apertura, 0, 5) >= substr($cierre, 0, 5)) {
        return 'La hora de apertura debe ser anterior a la hora de cierre.';
    }
    return validarTexto($descanso, 'Descanso', 0, 50, false);
}

// ------------------------------------------------------------
//  Validaciones por formulario (usan las funciones de arriba)
// ------------------------------------------------------------

/** Registro de cuenta. */
function validarDatosRegistro(array $d): string {
    return primerError([
        validarNombrePersona($d['primerNombre']   ?? '', 'Nombres'),
        validarNombrePersona($d['primerApellido'] ?? '', 'Apellidos'),
        validarUsuario($d['username'] ?? ''),
        validarEmail($d['email'] ?? ''),
        validarCelular($d['celular'] ?? '', false),
        validarPassword($d['password'] ?? '', $d['confirmPassword'] ?? ''),
    ]);
}

/** Perfil personal. */
function validarDatosPerfil(array $d): string {
    return primerError([
        validarNombrePersona($d['primerNombre']   ?? '', 'Nombre'),
        validarNombrePersona($d['primerApellido'] ?? '', 'Apellido'),
        validarCelular($d['celular'] ?? '', false),
        validarTexto($d['direccion'] ?? '', 'Dirección', 5, 150, false),
    ]);
}

/** Datos del local. */
function validarDatosLocal(array $d): string {
    return primerError([
        validarTexto($d['nomLocal']     ?? '', 'Nombre del local', 3, 100),
        validarTexto($d['describLocal'] ?? '', 'Descripción', 0, 300, false),
        validarTexto($d['ubiLocal']     ?? '', 'Número de local', 0, 150, false),
        validarCelular($d['urlWhatsapp'] ?? '', true),
    ]);
}

/** Datos de un producto (la categoría se valida aparte con validarCategoria). */
function validarDatosProducto(array $d): string {
    $seccion = trim($d['seccion'] ?? '');
    return primerError([
        validarTexto($d['nomProducto']     ?? '', 'Nombre del producto', 2, 80),
        validarTexto($d['describProducto'] ?? '', 'Descripción', 0, 200, false),
        validarPrecio((string)($d['precio'] ?? '')),
        validarTexto($d['talla'] ?? '', 'Tallas', 0, 30, false),
        ($seccion !== '' && !in_array($seccion, SECCIONES_PRODUCTO, true)) ? 'La sección elegida no es válida.' : '',
        validarFotos($d['fotosUrl'] ?? ''),
    ]);
}