<?php
// ============================================================
//  GALEZARZAL — Configuración de la plataforma (RF-22)
//  Lee y guarda parámetros generales en la tabla `configuracion`
//  (clave/valor). Claves: nombre_sitio, modo_mantenimiento,
//  registro_abierto, aprobacion_manual.
// ============================================================
require_once __DIR__ . '/config.php';

/** Valores por defecto si la tabla no existe o la clave falta. */
function configPorDefecto(): array {
    return [
        'nombre_sitio'       => defined('APP_NAME') ? APP_NAME : 'Galezarzal',
        'modo_mantenimiento' => '0',
        'registro_abierto'   => '1',
        'aprobacion_manual'  => '0',
    ];
}

/**
 * Lee la tabla `configuracion` UNA sola vez por petición (así el sitio no
 * hace una consulta por cada pregunta). Devuelve solo lo que hay en la BD.
 * Con $recargar = true vuelve a consultar (lo usa guardarConfig()).
 */
function cargarConfigBD(bool $recargar = false): array {
    static $filas = null;
    if ($filas === null || $recargar) {
        $filas = [];
        try {
            foreach (getDB()->query('SELECT clave, valor FROM configuracion') as $f) {
                $filas[$f['clave']] = $f['valor'];
            }
        } catch (PDOException $e) {
            registrarError('cargarConfigBD: ' . $e->getMessage());
        }
    }
    return $filas;
}

/** Devuelve el valor de una clave (o $porDefecto si no existe). */
function obtenerConfig(string $clave, $porDefecto = null) {
    $filas = cargarConfigBD();
    if (array_key_exists($clave, $filas)) return $filas[$clave];
    return $porDefecto ?? (configPorDefecto()[$clave] ?? null);
}

/** Devuelve toda la configuración como arreglo clave => valor. */
function obtenerTodaConfig(): array {
    return array_merge(configPorDefecto(), cargarConfigBD());
}

/** Nombre del sitio que guardó el administrador (si está vacío, APP_NAME). */
function nombreSitio(): string {
    $nombre = trim((string)obtenerConfig('nombre_sitio'));
    return $nombre !== '' ? $nombre : (defined('APP_NAME') ? APP_NAME : 'Galezarzal');
}

/**
 * Nombre del sitio listo para el logo del encabezado (ya escapado).
 * Con el nombre original conserva el estilo "Gale Zarzal"; con otro nombre
 * deja la última palabra resaltada.
 */
function nombreSitioLogoHtml(): string {
    $nombre = nombreSitio();
    if (strcasecmp($nombre, 'galezarzal') === 0) return 'Gale <span>Zarzal</span>';
    $e = fn(string $t) => htmlspecialchars($t, ENT_QUOTES, 'UTF-8');
    $palabras = preg_split('/\s+/u', $nombre) ?: [$nombre];
    if (count($palabras) < 2) return $e($nombre);
    $ultima = array_pop($palabras);
    return $e(implode(' ', $palabras)) . ' <span>' . $e($ultima) . '</span>';
}

/** Atajo: ¿el parámetro está activado ('1')? Ej.: configActiva('registro_abierto') */
function configActiva(string $clave): bool {
    return obtenerConfig($clave) === '1';
}

/** Guarda varios pares clave => valor en una sola transacción. */
function guardarConfig(array $pares): bool {
    $permitidas = array_keys(configPorDefecto());
    $db = getDB();
    try {
        $db->beginTransaction();
        $st = $db->prepare(
            'INSERT INTO configuracion (clave, valor) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE valor = VALUES(valor)'
        );
        foreach ($pares as $clave => $valor) {
            if (!in_array($clave, $permitidas, true)) continue;
            $st->execute([$clave, (string)$valor]);
        }
        $db->commit();
        cargarConfigBD(true); // la caché de esta petición ya no vale
        return true;
    } catch (PDOException $e) {
        if ($db->inTransaction()) $db->rollBack();
        registrarError('guardarConfig: ' . $e->getMessage());
        return false;
    }
}
