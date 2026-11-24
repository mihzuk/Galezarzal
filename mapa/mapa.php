<?php
// ============================================================
//  GALEZARZAL — Mapa interactivo de la Galería (#71)
//  Plano con los puestos numerados. Los que tienen un local activo
//  se pueden tocar: muestran el local, su categoría y si está
//  abierto ahora. Sin JavaScript, cada puesto lleva directo al local.
// ============================================================
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/aut_verifica.php';
require_once __DIR__ . '/../includes/horario.php';
require_once __DIR__ . '/planoGaleria.php';

// Ruta base del sitio. Cuando se fusione el #47 (rutas configurables) existirá BASE_PATH.
$base = defined('BASE_PATH') ? BASE_PATH : '/galezarzal';

$db = getDB();
$categorias = $db->query("SELECT idcategoria, nomCategoria FROM categoria ORDER BY nomCategoria")->fetchAll();
$nombreCategoria = array_column($categorias, 'nomCategoria', 'idcategoria');

// Tablas en minúscula para que funcione también en Linux (#16).
$locales = $db->query(
    "SELECT l.idLocal, l.nomLocal, l.ubiLocal,
            h.horarioApertura, h.horarioCierre, h.diasDisponibles,
            (SELECT p.idcategoria FROM producto p
              WHERE p.idLocal = l.idLocal AND p.estado = 'disponible'
              ORDER BY p.idProducto LIMIT 1) AS idCategoria,
            (SELECT COUNT(*) FROM producto p
              WHERE p.idLocal = l.idLocal AND p.estado = 'disponible') AS numProductos
     FROM local l
     LEFT JOIN horario h ON h.idHorario = l.idHorario
     WHERE l.estado = 'activo'
     ORDER BY l.fechaRegistro, l.idLocal"
)->fetchAll();

$plano = planoGaleria();
$puestosValidos = array_column($plano['puestos'], 'numero');

// Puesto => local. Si dos locales dicen el mismo puesto, queda el más antiguo.
$localPorPuesto = [];
$sinUbicar = 0;
foreach ($locales as $l) {
    $n = numeroPuesto($l['ubiLocal']);
    if ($n === null || !in_array($n, $puestosValidos, true) || isset($localPorPuesto[$n])) {
        $sinUbicar++;
        continue;
    }
    $l['abierto'] = localAbiertoAhora($l['diasDisponibles'], $l['horarioApertura'], $l['horarioCierre']);
    $l['categoria'] = $nombreCategoria[$l['idCategoria']] ?? 'Sin productos aún';
    $localPorPuesto[$n] = $l;
}
$abiertos = count(array_filter($localPorPuesto, fn($l) => $l['abierto']));

/** Texto del horario para la tarjeta: "LUN a SAB · 8:00 a. m. – 6:00 p. m." */
$textoHorario = function (array $l): string {
    $dias = diasDelHorario($l['diasDisponibles']);
    if (!$dias || !$l['horarioApertura'] || !$l['horarioCierre']) return 'Horario no registrado';
    $nombres = ['LUN' => 'Lun', 'MAR' => 'Mar', 'MIE' => 'Mié', 'JUE' => 'Jue', 'VIE' => 'Vie', 'SAB' => 'Sáb', 'DOM' => 'Dom'];
    $rango = count($dias) > 2 ? $nombres[$dias[0]] . ' a ' . $nombres[end($dias)] : implode(' y ', array_map(fn($d) => $nombres[$d], $dias));
    $hora = fn(string $h) => str_replace(['am', 'pm'], ['a. m.', 'p. m.'], date('g:i a', strtotime($h)));
    return $rango . ' · ' . $hora($l['horarioApertura']) . ' – ' . $hora($l['horarioCierre']);
};

$h = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');

$paginaActiva = 'locales';
$tituloPagina = 'Mapa de la Galería';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="hero mapa-hero">
  <p class="hero-sub">Galería de Zarzal</p>
  <h1>Recorre la Galería <span>desde tu celular</span></h1>
  <p>Toca un puesto para ver qué vende y si está abierto ahora.</p>
</section>

<section class="seccion mapa-seccion">

  <div class="mapa-controles" hidden>
    <div class="filtros" role="group" aria-label="Filtrar por categoría">
      <button type="button" class="filtro-btn activo" data-filtro-cat="" aria-pressed="true">Todos</button>
      <?php foreach ($categorias as $c): ?>
      <button type="button" class="filtro-btn" data-filtro-cat="<?= (int)$c['idcategoria'] ?>" aria-pressed="false"><?= $h($c['nomCategoria']) ?></button>
      <?php endforeach; ?>
    </div>
    <div class="mapa-controles-fila">
      <label class="mapa-switch">
        <input type="checkbox" id="soloAbiertos">
        <span>Solo abiertos ahora <strong>(<?= $abiertos ?>)</strong></span>
      </label>
      <div class="mapa-zoom" role="group" aria-label="Acercar o alejar el plano">
        <button type="button" class="btn-gris" data-zoom="-1" aria-label="Alejar">−</button>
        <button type="button" class="btn-gris" data-zoom="0" aria-label="Tamaño original">100 %</button>
        <button type="button" class="btn-gris" data-zoom="1" aria-label="Acercar">+</button>
      </div>
    </div>
  </div>

  <div class="mapa-contenedor" id="mapaContenedor">
    <svg class="mapa-svg" id="mapaSvg" viewBox="0 0 <?= $plano['ancho'] ?> <?= $plano['alto'] ?>" role="group" aria-labelledby="mapaTitulo">
      <title id="mapaTitulo">Plano de la Galería de Zarzal con <?= count($localPorPuesto) ?> locales en Galezarzal</title>
      <defs>
        <pattern id="toldo" width="16" height="16" patternUnits="userSpaceOnUse">
          <rect width="8" height="16" fill="#E94560"/>
          <rect x="8" width="8" height="16" fill="#F1FAEE"/>
        </pattern>
      </defs>

      <rect class="mapa-edificio" x="20" y="20" width="<?= $plano['ancho'] - 40 ?>" height="<?= $plano['alto'] - 40 ?>" rx="18"/>
      <?php foreach ($plano['pasillos'] as $p): ?>
      <rect class="mapa-pasillo" x="<?= $p['x'] ?>" y="<?= $p['y'] ?>" width="<?= $p['ancho'] ?>" height="<?= $p['alto'] ?>"/>
      <text class="mapa-pasillo-texto" x="<?= $p['x'] + $p['ancho'] / 2 ?>" y="<?= $p['y'] + $p['alto'] / 2 ?>"><?= $h($p['texto']) ?></text>
      <?php endforeach; ?>
      <?php foreach ($plano['entradas'] as $e): ?>
      <g class="mapa-entrada" transform="translate(<?= $e['x'] ?> <?= $e['y'] ?>)">
        <rect x="-14" y="-40" width="28" height="80" rx="6"/>
        <text transform="rotate(-90)" y="5"><?= $h($e['texto']) ?></text>
      </g>
      <?php endforeach; ?>

      <?php foreach ($plano['puestos'] as $p):
          $n = $p['numero'];
          $l = $localPorPuesto[$n] ?? null;
          $altoToldo = 14;
          $yToldo = $p['toldo'] === 'abajo' ? $p['y'] + $p['alto'] - $altoToldo : $p['y'];
          $cx = $p['x'] + $p['ancho'] / 2;
          $cy = $p['y'] + $p['alto'] / 2;
      ?>
      <?php if ($l): ?>
      <a class="puesto con-local<?= $l['abierto'] ? ' abierto' : '' ?>"
         href="<?= $h($base) ?>/local/verLocal.php?id=<?= (int)$l['idLocal'] ?>"
         data-id="<?= (int)$l['idLocal'] ?>"
         data-puesto="<?= $n ?>"
         data-nombre="<?= $h($l['nomLocal']) ?>"
         data-categoria="<?= $h($l['categoria']) ?>"
         data-cat="<?= (int)$l['idCategoria'] ?>"
         data-abierto="<?= $l['abierto'] ? '1' : '0' ?>"
         data-horario="<?= $h($textoHorario($l)) ?>"
         data-productos="<?= (int)$l['numProductos'] ?>"
         aria-label="Puesto <?= $n ?>: <?= $h($l['nomLocal']) ?>, <?= $h($l['categoria']) ?>, <?= $l['abierto'] ? 'abierto ahora' : 'cerrado ahora' ?>">
        <title>Puesto <?= $n ?> · <?= $h($l['nomLocal']) ?></title>
        <rect class="puesto-caja" x="<?= $p['x'] ?>" y="<?= $p['y'] ?>" width="<?= $p['ancho'] ?>" height="<?= $p['alto'] ?>" rx="8"/>
        <rect class="puesto-toldo" x="<?= $p['x'] ?>" y="<?= $yToldo ?>" width="<?= $p['ancho'] ?>" height="<?= $altoToldo ?>"/>
        <text class="puesto-numero" x="<?= $cx ?>" y="<?= $cy ?>"><?= $n ?></text>
        <circle class="puesto-estado" cx="<?= $cx ?>" cy="<?= $cy + 34 ?>" r="9"/>
      </a>
      <?php else: ?>
      <g class="puesto vacio" aria-hidden="true">
        <rect class="puesto-caja" x="<?= $p['x'] ?>" y="<?= $p['y'] ?>" width="<?= $p['ancho'] ?>" height="<?= $p['alto'] ?>" rx="8"/>
        <text class="puesto-numero" x="<?= $cx ?>" y="<?= $cy ?>"><?= $n ?></text>
      </g>
      <?php endif; ?>
      <?php endforeach; ?>
    </svg>
  </div>

  <ul class="mapa-leyenda" aria-label="Convenciones del plano">
    <li><span class="ley ley-abierto"></span> Abierto ahora</li>
    <li><span class="ley ley-cerrado"></span> Cerrado ahora</li>
    <li><span class="ley ley-vacio"></span> Puesto sin local en Galezarzal</li>
  </ul>

  <div class="mapa-tarjeta" id="mapaTarjeta" aria-live="polite">
    <p class="text-gris" id="mapaAyuda">Toca un puesto con color para ver el local.</p>
  </div>

  <p class="mapa-nota text-gris">
    Plano ilustrativo: la ubicación de los puestos es aproximada.
    <?php if ($sinUbicar > 0): ?>
      <?= $sinUbicar ?> <?= $sinUbicar === 1 ? 'local no aparece' : 'locales no aparecen' ?> en el plano porque su ubicación no coincide con un puesto;
      <a href="<?= $h($base) ?>/local/buscarLocal.php" class="text-rojo">míralos en Locales</a>.
    <?php endif; ?>
  </p>
</section>

<script>
(function () {
  var svg = document.getElementById('mapaSvg');
  var contenedor = document.getElementById('mapaContenedor');
  var tarjeta = document.getElementById('mapaTarjeta');
  var controles = document.querySelector('.mapa-controles');
  var base = <?= json_encode($base, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  var puestos = Array.prototype.slice.call(svg.querySelectorAll('.puesto.con-local'));
  var catActiva = '';
  var zoom = 1;

  controles.hidden = false; // Los controles solo sirven con JavaScript.

  function texto(etiqueta, clase, contenido) {
    var el = document.createElement(etiqueta);
    if (clase) el.className = clase;
    el.textContent = contenido;
    return el;
  }

  function mostrar(puesto) {
    puestos.forEach(function (p) { p.classList.remove('seleccionado'); });
    puesto.classList.add('seleccionado');
    var d = puesto.dataset;
    var productos = parseInt(d.productos, 10);

    tarjeta.textContent = '';
    var cab = document.createElement('div');
    cab.className = 'mapa-tarjeta-cab';
    cab.appendChild(texto('span', 'card-badge badge-mixto', d.categoria));
    cab.appendChild(texto('span', 'card-badge ' + (d.abierto === '1' ? 'badge-activo' : 'badge-agotado'), d.abierto === '1' ? 'Abierto ahora' : 'Cerrado'));
    tarjeta.appendChild(cab);
    tarjeta.appendChild(texto('h2', 'mapa-tarjeta-nombre', d.nombre));
    tarjeta.appendChild(texto('p', 'text-gris', 'Puesto ' + d.puesto + ' · ' + d.horario));
    tarjeta.appendChild(texto('p', 'text-gris', productos === 0 ? 'Pronto con productos' : (productos === 1 ? '1 producto' : productos + ' productos')));
    var enlace = texto('a', 'btn-primary', 'Ver catálogo');
    enlace.href = base + '/local/verLocal.php?id=' + encodeURIComponent(d.id);
    tarjeta.appendChild(enlace);

    if (window.matchMedia('(max-width: 768px)').matches) {
      tarjeta.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
  }

  puestos.forEach(function (p) {
    // Un clic o Enter abre la tarjeta en vez de ir directo al local.
    p.addEventListener('click', function (e) { e.preventDefault(); mostrar(p); });
  });

  function aplicarFiltros() {
    var soloAbiertos = document.getElementById('soloAbiertos').checked;
    puestos.forEach(function (p) {
      var fuera = (catActiva && p.dataset.cat !== catActiva) || (soloAbiertos && p.dataset.abierto !== '1');
      p.classList.toggle('atenuado', fuera);
    });
    svg.classList.toggle('resaltar-abiertos', soloAbiertos);
  }

  document.getElementById('soloAbiertos').addEventListener('change', aplicarFiltros);

  Array.prototype.forEach.call(document.querySelectorAll('[data-filtro-cat]'), function (b) {
    b.addEventListener('click', function () {
      catActiva = b.dataset.filtroCat;
      Array.prototype.forEach.call(document.querySelectorAll('[data-filtro-cat]'), function (o) {
        var activo = o === b;
        o.classList.toggle('activo', activo);
        o.setAttribute('aria-pressed', activo ? 'true' : 'false');
      });
      aplicarFiltros();
    });
  });

  Array.prototype.forEach.call(document.querySelectorAll('[data-zoom]'), function (b) {
    b.addEventListener('click', function () {
      var paso = parseInt(b.dataset.zoom, 10);
      zoom = paso === 0 ? 1 : Math.min(3, Math.max(1, zoom + paso * 0.5));
      contenedor.style.setProperty('--zoom', zoom);
    });
  });

  // Enlace directo a un puesto: mapa.php?puesto=17 abre su tarjeta (lo puede usar la página del local).
  var pedido = new URLSearchParams(window.location.search).get('puesto');
  if (pedido) {
    var elegido = puestos.filter(function (p) { return p.dataset.puesto === pedido; })[0];
    if (elegido) mostrar(elegido);
  }
})();
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
