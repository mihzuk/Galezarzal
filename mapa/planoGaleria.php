<?php
// ============================================================
//  GALEZARZAL — Plano de la Galería de Zarzal (#71)
//
//  Contorno y calles REALES, tomados de OpenStreetMap (vía 236656577,
//  «Galería Principal», Carrera 12, Zarzal; © colaboradores de
//  OpenStreetMap, licencia ODbL):
//    - Manzana casi rectangular de unos 80 m (lados sobre las carreras)
//      por 86 m (lados sobre las calles).
//    - Carrera 12 al sureste (la de la dirección), Carrera 13 al noroeste,
//      Calle 10 al suroeste y Calle 11 al noreste.
//    - El plano se dibuja con la Carrera 12 abajo; el norte real queda
//      girado 55° en sentido horario respecto a «arriba».
//
//  La distribución de los puestos POR DENTRO es APROXIMADA: no hay un
//  croquis publicado. Si se consigue uno, solo hay que cambiar el arreglo
//  de puestos de esta función; mapa.php dibuja lo que esta función diga.
//
//  Escala: 10 unidades del SVG = 1 metro. viewBox 0 0 1000 1060.
// ============================================================

/**
 * @return array{
 *   ancho:int, alto:int,
 *   edificio:array{x:int, y:int, ancho:int, alto:int},
 *   calles:array<int, array{texto:string, x:float, y:float, rotar:int}>,
 *   norte:array{x:int, y:int, grados:int},
 *   entradas:array<int, array{x:float, y:float, ancho:float, alto:float, texto:string}>,
 *   pasillos:array<int, array{x:float, y:float, texto:string}>,
 *   puestos:array<int, array{numero:int, x:float, y:float, ancho:float, alto:float, toldo:string}>
 * }  `toldo` es el lado del puesto que da al pasillo: arriba, abajo, izquierda o derecha.
 */
function planoGaleria(): array {
    // Edificio: 80 m de ancho (Carrera 12 / Carrera 13) por 86 m de alto (Calle 10 / Calle 11).
    $ex = 100; $ey = 100; $ew = 800; $eh = 860;
    $borde = 20;          // muro
    $fondo = 120;         // profundidad de los puestos del perímetro
    $sep = 8;             // separación entre puestos

    $xIni = $ex + $borde;              // 120
    $xFin = $ex + $ew - $borde;        // 880
    $yIni = $ey + $borde;              // 120
    $yFin = $ey + $eh - $borde;        // 940

    // Entrada principal sobre la Carrera 12, en el centro del lado de abajo.
    $entradaAncho = 140;
    $entradaX = $ex + $ew / 2 - $entradaAncho / 2;   // 430

    $puestos = [];
    $agregar = function (float $x, float $y, float $w, float $h, string $toldo) use (&$puestos) {
        $puestos[] = ['numero' => count($puestos) + 1, 'x' => $x, 'y' => $y, 'ancho' => $w, 'alto' => $h, 'toldo' => $toldo];
    };

    // Se numeran en el sentido del reloj, empezando junto a la entrada.
    // 1-3: Carrera 12, a la izquierda de la entrada (de la entrada hacia la esquina).
    $anchoAbajo = ($entradaX - $xIni - 2 * $sep - $sep) / 3;
    for ($i = 0; $i < 3; $i++) {
        $agregar($entradaX - $sep - ($i + 1) * $anchoAbajo - $i * $sep, $yFin - $fondo, $anchoAbajo, $fondo, 'arriba');
    }
    // 4-7: lado de la Calle 10, de abajo hacia arriba.
    $yColIni = $yIni + $fondo + $sep;
    $yColFin = $yFin - $fondo - $sep;
    $altoLado = ($yColFin - $yColIni - 3 * $sep) / 4;
    for ($i = 0; $i < 4; $i++) {
        $agregar($xIni, $yColFin - ($i + 1) * $altoLado - $i * $sep, $fondo, $altoLado, 'derecha');
    }
    // 8-13: lado de la Carrera 13, de izquierda a derecha.
    $anchoArriba = ($xFin - $xIni - 5 * $sep) / 6;
    for ($i = 0; $i < 6; $i++) {
        $agregar($xIni + $i * ($anchoArriba + $sep), $yIni, $anchoArriba, $fondo, 'abajo');
    }
    // 14-17: lado de la Calle 11, de arriba hacia abajo.
    for ($i = 0; $i < 4; $i++) {
        $agregar($xFin - $fondo, $yColIni + $i * ($altoLado + $sep), $fondo, $altoLado, 'izquierda');
    }
    // 18-20: Carrera 12, a la derecha de la entrada (de la esquina hacia la entrada).
    for ($i = 0; $i < 3; $i++) {
        $agregar($xFin - ($i + 1) * $anchoAbajo - $i * $sep, $yFin - $fondo, $anchoAbajo, $fondo, 'arriba');
    }
    // 21-24: isla central de 2 x 2, con el toldo hacia el pasillo que la rodea.
    $islaW = 300; $islaH = 300;
    $islaX = $ex + $ew / 2 - $islaW / 2;   // 350
    $islaY = $ey + $eh / 2 - $islaH / 2;   // 380
    $pw = ($islaW - $sep) / 2; $ph = ($islaH - $sep) / 2;
    $agregar($islaX,             $islaY,             $pw, $ph, 'arriba');
    $agregar($islaX + $pw + $sep, $islaY,             $pw, $ph, 'arriba');
    $agregar($islaX + $pw + $sep, $islaY + $ph + $sep, $pw, $ph, 'abajo');
    $agregar($islaX,             $islaY + $ph + $sep, $pw, $ph, 'abajo');

    return [
        'ancho'    => 1000,
        'alto'     => 1060,
        'edificio' => ['x' => $ex, 'y' => $ey, 'ancho' => $ew, 'alto' => $eh],
        'calles'   => [
            ['texto' => 'Carrera 12', 'x' => 500, 'y' => 1015, 'rotar' => 0],
            ['texto' => 'Carrera 13', 'x' => 500, 'y' => 55,   'rotar' => 0],
            ['texto' => 'Calle 10',   'x' => 50,  'y' => 530,  'rotar' => -90],
            ['texto' => 'Calle 11',   'x' => 950, 'y' => 530,  'rotar' => 90],
        ],
        // Flecha del norte: «arriba» del plano girado 55° en sentido horario.
        'norte'    => ['x' => 68, 'y' => 68, 'grados' => 55],
        'entradas' => [
            ['x' => $entradaX, 'y' => $ey + $eh - $borde, 'ancho' => $entradaAncho, 'alto' => $borde * 2, 'texto' => 'Entrada'],
        ],
        'pasillos' => [
            ['x' => 500, 'y' => ($yIni + $fondo + $islaY) / 2,          'texto' => 'Pasillo'],
            ['x' => 500, 'y' => ($islaY + $islaH + $yFin - $fondo) / 2, 'texto' => 'Pasillo'],
        ],
        'puestos'  => $puestos,
    ];
}
