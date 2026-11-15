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

/** Devuelve el valor de una clave (o $porDefecto si no existe). */
function obtenerConfig(string $clave, $porDefecto = null) {
    try {
        $st = getDB()->prepare('SELECT valor FROM configuracion WHERE clave = ?');
        $st->execute([$clave]);
        $valor = $st->fetchColumn();
        if ($valor !== false) return $valor;
    } catch (PDOException $e) {
        registrarError('obtenerConfig(' . $clave . '): ' . $e->getMessage());
    }
    return $porDefecto ?? (configPorDefecto()[$clave] ?? null);
}

/** Devuelve toda la configuración como arreglo clave => valor. */
function obtenerTodaConfig(): array {
    $cfg = configPorDefecto();
    try {
        foreach (getDB()->query('SELECT clave, valor FROM configuracion') as $f) {
            $cfg[$f['clave']] = $f['valor'];
        }
    } catch (PDOException $e) {
        registrarError('obtenerTodaConfig: ' . $e->getMessage());
    }
    return $cfg;
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
        return true;
    } catch (PDOException $e) {
        if ($db->inTransaction()) $db->rollBack();
        registrarError('guardarConfig: ' . $e->getMessage());
        return false;
    }
}
