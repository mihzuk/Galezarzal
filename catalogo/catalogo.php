<?php
// ============================================================
//  GALEZARZAL — Ver detalle de un producto
// ============================================================
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/aut_verifica.php';
require_once __DIR__ . '/../includes/aut_visualizar_error.php';

$idProducto = (int)($_GET['id'] ?? 0);
if (!$idProducto) mostrarError('Error', 'Producto no especificado.');

$db = getDB();
$st = $db->prepare(
    "SELECT p.*, c.nomCategoria, l.nomLocal, l.idLocal, l.urlWhatsapp, l.ubiLocal
     FROM Producto p
     JOIN Categoria c ON c.idcategoria = p.idcategoria
     JOIN Local l ON l.idLocal = p.idLocal
     WHERE p.idProducto = ? AND p.estado != 'inactivo'"
);
$st->execute([$idProducto]);
$producto = $st->fetch();
if (!$producto) mostrarError('Producto no encontrado', 'Este producto no está disponible.');

// Otros productos del mismo local
$stOtros = $db->prepare(
    "SELECT idProducto, nomProducto, precio, fotosUrl, talla, estado
     FROM Producto
     WHERE idLocal = ? AND idProducto != ? AND estado = 'disponible'
     ORDER BY RAND() LIMIT 4"
);
$stOtros->execute([$producto['idLocal'], $idProducto]);
$otros = $stOtros->fetchAll();

$fotos = json_decode($producto['fotosUrl'] ?? '[]', true) ?: [];
$msgWA = urlencode("Hola, vi el producto «{$producto['nomProducto']}» en Galezarzal y me interesa. ¿Está disponible?");

$paginaActiva = 'locales';
$tituloPagina = $producto['nomProducto'];
require_once __DIR__ . '/../includes/header.php';
?>

<section class="seccion" style="max-width:900px;">
  <p style="font-size:.85rem;color:var(--gris);margin-bottom:1rem;">
    <a href="/galezarzal/local/buscarLocal.php">Locales</a> ›
    <a href="/galezarzal/local/verLocal.php?id=<?= $producto['idLocal'] ?>"><?= htmlspecialchars($producto['nomLocal']) ?></a> ›
    <?= htmlspecialchars($producto['nomProducto']) ?>
  </p>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;align-items:start;">
    <!-- Fotos -->
    <div>
      <?php if (!empty($fotos[0])): ?>
        <img src="<?= htmlspecialchars($fotos[0]) ?>" alt="<?= htmlspecialchars($producto['nomProducto']) ?>"
             style="width:100%;border-radius:var(--radio);box-shadow:var(--sombra);object-fit:cover;max-height:400px;">
        <?php if (count($fotos) > 1): ?>
        <div style="display:flex;gap:.5rem;margin-top:.75rem;flex-wrap:wrap;">
          <?php foreach (array_slice($fotos, 1, 4) as $f): ?>
          <img src="<?= htmlspecialchars($f) ?>" style="width:70px;height:70px;object-fit:cover;border-radius:6px;cursor:pointer;border:2px solid #eee;">
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      <?php else: ?>
        <div style="width:100%;height:300px;background:var(--crema);border-radius:var(--radio);display:flex;align-items:center;justify-content:center;color:var(--gris);font-size:3rem;">👗</div>
      <?php endif; ?>
    </div>

    <!-- Info -->
    <div>
      <span class="card-badge badge-mixto"><?= htmlspecialchars($producto['nomCategoria']) ?></span>
      <?php if (!empty($producto['seccion'])): ?>
        <span class="card-badge badge-hombre" style="margin-left:.3rem;"><?= htmlspecialchars($producto['seccion']) ?></span>
      <?php endif; ?>

      <h1 style="font-size:1.5rem;margin:.5rem 0;"><?= htmlspecialchars($producto['nomProducto']) ?></h1>

      <p style="font-size:2rem;font-weight:700;color:var(--rojo);margin:.5rem 0;">
        $<?= number_format($producto['precio'], 0, ',', '.') ?>
      </p>

      <?php if ($producto['estado'] === 'agotado'): ?>
        <span class="card-badge badge-agotado" style="font-size:.85rem;padding:.3rem .8rem;">AGOTADO</span>
      <?php else: ?>
        <span class="card-badge badge-activo" style="font-size:.85rem;padding:.3rem .8rem;">DISPONIBLE</span>
      <?php endif; ?>

      <?php if (!empty($producto['talla'])): ?>
      <p style="margin-top:1rem;"><strong>Tallas disponibles:</strong> <?= htmlspecialchars($producto['talla']) ?></p>
      <?php endif; ?>

      <?php if (!empty($producto['color'])): ?>
      <p style="margin-top:.4rem;"><strong>Color(es):</strong> <?= htmlspecialchars($producto['color']) ?></p>
      <?php endif; ?>

      <?php if (!empty($producto['describProducto'])): ?>
      <p style="margin-top:1rem;color:#555;line-height:1.6;"><?= nl2br(htmlspecialchars($producto['describProducto'])) ?></p>
      <?php endif; ?>

      <div style="margin-top:1.5rem;display:flex;gap:.75rem;flex-wrap:wrap;">
        <?php if ($producto['estado'] !== 'agotado'): ?>
        <a href="https://wa.me/<?= htmlspecialchars($producto['urlWhatsapp']) ?>?text=<?= $msgWA ?>"
           target="_blank" class="btn-primary" style="background:#25D366;border-color:#25D366;">
          💬 Comprar por WhatsApp
        </a>
        <?php endif; ?>
        <a href="/galezarzal/local/verLocal.php?id=<?= $producto['idLocal'] ?>" class="btn-outline">Ver todo el local</a>
      </div>

      <div style="margin-top:1.25rem;padding:.75rem;background:var(--crema);border-radius:var(--radio);font-size:.85rem;">
        📍 <?= htmlspecialchars($producto['nomLocal']) ?> — <?= htmlspecialchars($producto['ubiLocal'] ?? 'Galería de Zarzal') ?>
      </div>
    </div>
  </div>

  <!-- Más productos del local -->
  <?php if (!empty($otros)): ?>
  <div style="margin-top:3rem;">
    <h2 style="font-size:1.1rem;font-weight:700;margin-bottom:1rem;">Más de <span style="color:var(--rojo);"><?= htmlspecialchars($producto['nomLocal']) ?></span></h2>
    <div class="cards-grid">
      <?php foreach ($otros as $o): ?>
      <?php $oFotos = json_decode($o['fotosUrl'] ?? '[]', true) ?: []; ?>
      <a href="/galezarzal/catalogo/catalogo.php?id=<?= $o['idProducto'] ?>" class="card">
        <?php if (!empty($oFotos[0])): ?>
          <img src="<?= htmlspecialchars($oFotos[0]) ?>" style="width:100%;height:120px;object-fit:cover;border-radius:6px;margin-bottom:.5rem;">
        <?php endif; ?>
        <h3 style="font-size:.9rem;"><?= htmlspecialchars($o['nomProducto']) ?></h3>
        <p style="color:var(--rojo);font-weight:700;">$<?= number_format($o['precio'], 0, ',', '.') ?></p>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
