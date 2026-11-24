<?php
// ============================================================
//  GALEZARZAL — Plano de la Galería de Zarzal (#71)
//
//  Distribución PROVISIONAL de los puestos: dos filas de 12 a lado
//  y lado de un pasillo central, con entradas en los extremos.
//  Cuando el PO entregue el croquis real, solo hay que cambiar los
//  datos de planoGaleria(); mapa.php dibuja lo que esta función diga.
//
//  Coordenadas en el sistema del SVG (viewBox 0 0 1000 560).
// ============================================================

/**
 * @return array{ancho:int, alto:int, puestos:array<int, array{numero:int, x:float, y:float, ancho:float, alto:float, toldo:string}>, pasillos:array, entradas:array}
 *         `toldo` indica en qué borde del puesto va el toldo: el que da al pasillo.
 */
function planoGaleria(): array {
    $ancho = 1000; $alto = 560;
    $margen = 40; $separacion = 8; $porFila = 12;
    $anchoPuesto = ($ancho - 2 * $margen - ($porFila - 1) * $separacion) / $porFila;
    $altoPuesto  = 170;

    $puestos = [];
    for ($i = 0; $i < $porFila; $i++) {
        $x = $margen + $i * ($anchoPuesto + $separacion);
        // Fila de arriba: 1 a 12, el toldo mira hacia abajo (al pasillo).
        $puestos[] = ['numero' => $i + 1, 'x' => $x, 'y' => 40, 'ancho' => $anchoPuesto, 'alto' => $altoPuesto, 'toldo' => 'abajo'];
        // Fila de abajo: 13 a 24, el toldo mira hacia arriba.
        $puestos[] = ['numero' => $porFila + $i + 1, 'x' => $x, 'y' => $alto - 40 - $altoPuesto, 'ancho' => $anchoPuesto, 'alto' => $altoPuesto, 'toldo' => 'arriba'];
    }
    usort($puestos, fn($a, $b) => $a['numero'] <=> $b['numero']);

    return [
        'ancho'    => $ancho,
        'alto'     => $alto,
        'puestos'  => $puestos,
        'pasillos' => [['x' => 22, 'y' => 40 + $altoPuesto, 'ancho' => $ancho - 44, 'alto' => $alto - 80 - 2 * $altoPuesto, 'texto' => 'Pasillo central']],
        'entradas' => [
            ['x' => 20,          'y' => $alto / 2, 'texto' => 'Entrada'],
            ['x' => $ancho - 20, 'y' => $alto / 2, 'texto' => 'Entrada'],
        ],
    ];
}
