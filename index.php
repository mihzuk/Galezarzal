<?php
// ============================================================
//  GALEZARZAL — Index / Inicio
// ============================================================
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/aut_verifica.php';

$paginaActiva = 'inicio';
$tituloPagina = 'Inicio';

// Locales recomendados (activos)
$locales = [];
try {
    $st = getDB()->query(
        "SELECT l.idLocal, l.nomLocal, l.describLocal, l.ubiLocal,
                c.nomCategoria
         FROM Local l
         JOIN Categoria c ON c.idcategoria = (
             SELECT p.idcategoria FROM Producto p
             WHERE p.idLocal = l.idLocal LIMIT 1
         )
         WHERE l.estado = 'activo'
         ORDER BY l.fechaRegistro DESC
         LIMIT 6"
    );
    $locales = $st->fetchAll();
} catch(Exception $e) { /* sin locales */ }

require_once __DIR__ . '/includes/header.php';
?>

<!-- HERO -->
<section class="hero">
  <div class="hero-inner">
    <p class="hero-sub">Moda local — Galería de Zarzal</p>
    <h1>Encuentra tu ropa favorita,<br><span>todo en un lugar</span></h1>
    <p>La vitrina digital que conecta compradores con los emprendedores
       textiles de la Galería de Zarzal.</p>
    <div class="hero-btns">
      <a href="/galezarzal/local/buscarLocal.php" class="btn-primary">buscar locales</a>
      <?php if (sesionActiva()): ?>
        <a href="/galezarzal/local/crearLocal.php" class="btn-outline">tengo un local</a>
      <?php else: ?>
        <a href="/galezarzal/usuario/ingresarCuenta.php" class="btn-outline">tengo un local</a>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- SECCIÓN ¿QUÉ ES? -->
<section class="seccion" style="background:var(--blanco);">
  <h2 style="font-size:1.2rem;font-weight:700;margin-bottom:1rem;">¿Qué es Galezarzal?</h2>
  <p style="max-width:720px;color:#555;line-height:1.7;margin-bottom:1.5rem;">
    Galezarzal es un marketplace especializado en moda local. Explora el catálogo de todos los
    locales de la Galería desde tu celular, compara estilos, tallas y precios sin salir de casa.
  </p>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:1rem;">
    <?php foreach ([
      ['Para compradores',   'Encuentra ropa de mujer, hombre y niños en segundos.'],
      ['Para vendedores',    'Publica tu catálogo y llega a más clientes todos los días.'],
      ['Local',              'Solo locales de la Galería de Zarzal, Valle del Cauca.'],
      ['Contacto directo',   'Habla con el vendedor sin intermediarios.'],
    ] as [$tit, $desc]): ?>
    <div style="background:var(--crema);border-radius:var(--radio);padding:1rem;">
      <strong><?= $tit ?></strong>
      <p style="font-size:.85rem;color:#666;margin-top:.35rem;"><?= $desc ?></p>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- LOCALES RECOMENDADOS -->
<section class="seccion">
  <div class="flex-between mb-2">
    <h2 class="seccion-titulo">Locales <span>recomendados</span></h2>
    <a href="/galezarzal/local/buscarLocal.php" class="btn-outline btn-sm">Ver más locales</a>
  </div>

  <?php if (empty($locales)): ?>
  <p class="text-gris">Aún no hay locales registrados. ¡Sé el primero en publicar!</p>
  <?php else: ?>
  <div class="cards-grid">
    <?php foreach ($locales as $loc): ?>
    <a href="/galezarzal/local/verLocal.php?id=<?= $loc['idLocal'] ?>" class="card">
      <span class="card-badge badge-mixto"><?= htmlspecialchars($loc['nomCategoria'] ?? 'Mixto') ?></span>
      <h3><?= htmlspecialchars($loc['nomLocal']) ?></h3>
      <p><?= htmlspecialchars($loc['describLocal'] ?? '') ?></p>
    </a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>