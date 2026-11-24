<?php
// ============================================================
//  GALEZARZAL — Utilidades de horario y ubicación de los locales
//  Las usan el mapa de la Galería (#71) y, más adelante, las
//  tarjetas de local (#59) y la página del local (#61).
// ============================================================

/** Zona horaria de Zarzal. Se fija aquí para no depender del php.ini del servidor. */
const ZONA_HORARIA_GALERIA = 'America/Bogota';

/** Códigos de día tal como se guardan en `horario.diasDisponibles`, en el orden de date('N') (1 = lunes). */
const DIAS_HORARIO = [1 => 'LUN', 2 => 'MAR', 3 => 'MIE', 4 => 'JUE', 5 => 'VIE', 6 => 'SAB', 7 => 'DOM'];

/**
 * Convierte "LUN,MAR,MIE" en ['LUN', 'MAR', 'MIE'].
 * Acepta minúsculas y espacios; descarta lo que no sea un día válido.
 */
function diasDelHorario(?string $dias): array {
    if ($dias === null || trim($dias) === '') return [];
    $lista = array_map(fn($d) => strtoupper(trim($d)), explode(',', $dias));
    return array_values(array_intersect($lista, DIAS_HORARIO));
}

/**
 * ¿El local está abierto en este momento?
 *
 * @param string|null $dias      Días de atención, p. ej. "LUN,MAR,MIE,JUE,VIE,SAB".
 * @param string|null $apertura  Hora de apertura "HH:MM[:SS]".
 * @param string|null $cierre    Hora de cierre "HH:MM[:SS]". Si es menor que la apertura,
 *                               el horario pasa la medianoche (p. ej. 18:00 a 02:00).
 * @param DateTimeInterface|null $ahora Momento a evaluar; por defecto, la hora actual en Zarzal.
 */
function localAbiertoAhora(?string $dias, ?string $apertura, ?string $cierre, ?DateTimeInterface $ahora = null): bool {
    $diasLocal = diasDelHorario($dias);
    if (!$diasLocal || !$apertura || !$cierre) return false;

    $zona  = new DateTimeZone(ZONA_HORARIA_GALERIA);
    $ahora = $ahora ? (new DateTimeImmutable('@' . $ahora->getTimestamp()))->setTimezone($zona)
                    : new DateTimeImmutable('now', $zona);

    $minAhora    = (int)$ahora->format('G') * 60 + (int)$ahora->format('i');
    $minApertura = minutosDelDia($apertura);
    $minCierre   = minutosDelDia($cierre);
    if ($minApertura === null || $minCierre === null || $minApertura === $minCierre) return false;

    $hoy  = DIAS_HORARIO[(int)$ahora->format('N')];
    $ayer = DIAS_HORARIO[(int)$ahora->modify('-1 day')->format('N')];

    if ($minApertura < $minCierre) {
        // Horario normal dentro del mismo día. A la hora exacta de cierre ya está cerrado.
        return in_array($hoy, $diasLocal, true) && $minAhora >= $minApertura && $minAhora < $minCierre;
    }
    // Pasa la medianoche: abierto desde la apertura de hoy, o hasta el cierre del turno que empezó ayer.
    return (in_array($hoy, $diasLocal, true) && $minAhora >= $minApertura)
        || (in_array($ayer, $diasLocal, true) && $minAhora < $minCierre);
}

/** "08:30:00" o "08:30" → 510. Devuelve null si el formato no es válido. */
function minutosDelDia(string $hora): ?int {
    if (!preg_match('/^(\d{1,2}):(\d{2})(?::\d{2})?$/', trim($hora), $m)) return null;
    $h = (int)$m[1]; $min = (int)$m[2];
    if ($h > 23 || $min > 59) return null;
    return $h * 60 + $min;
}

/**
 * Número de puesto en la Galería a partir de `local.ubiLocal`.
 * "Local 5" → 5, "local 12B" → 12, "Puesto 3, segundo piso" → 3. Sin número → null.
 */
function numeroPuesto(?string $ubicacion): ?int {
    if ($ubicacion === null || !preg_match('/\d+/', $ubicacion, $m)) return null;
    $n = (int)$m[0];
    return $n > 0 ? $n : null;
}
