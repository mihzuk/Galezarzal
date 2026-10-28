<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/aut_verifica.php';
require_once __DIR__ . '/../includes/aut_visualizar_error.php';

$idLocal = (int)($_GET['id'] ?? 0);
if (!$idLocal) mostrarError('Error', 'Local no especificado.');

$db = getDB();
$st = $db->prepare(
    "SELECT l.*, h.horarioApertura, h.horarioCierre, h.diasDisponibles
     FROM Local l
     LEFT JOIN Horario h ON h.idHorario = l.idHorario
     WHERE l.idLocal = ? AND l.estado = 'activo'"
);
$st->execute([$idLocal]);
$local = $st->fetch();
if (!$local) mostrarError('Local no encontrado', 'Este local no existe o no está disponible.');

// Productos
$stProd = $db->prepare(
    "SELECT p.*, c.nomCategoria FROM Producto p
     JOIN Categoria c ON c.idcategoria=p.idcategoria
     WHERE p.idLocal=? AND p.estado='disponible'
     ORDER BY p.nomProducto"
);
$stProd->execute([$idLocal]);
$productos = $stProd->fetchAll();

// ¿Es favorito del usuario actual?
$esFavorito = false;
if (sesionActiva()) {
    $stFav = $db->prepare("SELECT 1 FROM Favorito WHERE iduser=? AND idLocal=? LIMIT 1");
    $stFav->execute([$_SESSION['iduser'], $idLocal]);
    $esFavorito = (bool)$stFav->fetchColumn();
}

// Toggle favorito
if ($_SERVER['REQUEST_METHOD'] === 'POST' && sesionActiva()) {
    if ($esFavorito) {
        $db->prepare("DELETE FROM Favorito WHERE iduser=? AND idLocal=?")->execute([$_SESSION['iduser'],$idLocal]);
    } else {
        $db->prepare("INSERT INTO Favorito (iduser,idLocal) VALUES (?,?)")->execute([$_SESSION['iduser'],$idLocal]);
    }
    header("Location: /galezarzal/local/verLocal.php?id=$idLocal");
    exit;
}

$paginaActiva = 'locales'; $tituloPagina = $local['nomLocal'];
require_once __DIR__ . '/../includes/header.php';
?>

<div class="local-hero">
  <h1><?= htmlspecialchars($local['nomLocal']) ?></h1>
  <div class="local-meta">
    <span><?= htmlspecialchars($local['ubiLocal']??'') ?></span>
    <?php if ($local['diasDisponibles']): ?>
    <span class="horario"><?= htmlspecialchars($local['diasDisponibles']) ?> <?= date('g:ia', strtotime($local['horarioApertura']??'08:00')) ?> - <?= date('g:ia', strtotime($local['horarioCierre']??'18:00')) ?></span>
    <?php endif; ?>
  </div>
</div>

<section class="seccion">
  <div class="flex-between mb-2" style="flex-wrap:wrap;gap:1rem;">
    <div>
      <h2 style="font-size:1rem;margin-bottom:.4rem;">Sobre el local</h2>
      <p style="color:#555;max-width:600px;"><?= nl2br(htmlspecialchars($local['describLocal']??'')) ?></p>
    </div>
    <div style="display:flex;gap:.75rem;align-items:center;flex-wrap:wrap;">
      <?php if (sesionActiva()): ?>
      <form method="POST" style="display:inline;">
        <button type="submit" class="<?= $esFavorito ? 'btn-primary' : 'btn-outline' ?>">
          <?= $esFavorito ? '❤ Eliminar de favoritos' : '♡ Agregar a favoritos' ?>
        </button>
      </form>
      <?php endif; ?>
      <a href="https://wa.me/<?= htmlspecialchars($local['urlWhatsapp']) ?>"
         target="_blank" class="btn-primary" style="background:#25D366;border-color:#25D366;">
        💬 WhatsApp
      </a>
    </div>
  </div>

  <h3 style="font-size:1rem;font-weight:700;margin-bottom:1rem;">Catálogo de productos</h3>
  <?php if (empty($productos)): ?>
  <p class="text-gris">Este local aún no tiene productos disponibles.</p>
  <?php else: ?>
  <div class="cards-grid">
    <?php foreach ($productos as $p): ?>
    <a href="/galezarzal/catalogo/catalogo.php?id=<?= $p['idProducto'] ?>" class="card">
      <?php if ($p['fotosUrl']): ?>
        <?php $fotos = json_decode($p['fotosUrl'], true); ?>
        <?php if (!empty($fotos[0])): ?>
        <img src="<?= htmlspecialchars($fotos[0]) ?>" alt="<?= htmlspecialchars($p['nomProducto']) ?>" style="border-radius:6px;margin-bottom:.75rem;height:140px;object-fit:cover;width:100%;">
        <?php endif; ?>
      <?php endif; ?>
      <span class="card-badge badge-mixto" style="font-size:.7rem;"><?= htmlspecialchars($p['seccion']??'') ?></span>
      <h3 style="font-size:.95rem;"><?= htmlspecialchars($p['nomProducto']) ?></h3>
      <p style="color:var(--rojo);font-weight:700;margin:.25rem 0;">$<?= number_format($p['precio'],0,',','.') ?></p>
      <?php if ($p['estado'] === 'agotado'): ?>
        <span class="card-badge badge-agotado">AGOTADO</span>
      <?php else: ?>
        <p style="font-size:.78rem;color:var(--gris);">Tallas: <?= htmlspecialchars($p['talla']??'Única') ?></p>
      <?php endif; ?>
      <button class="btn-outline btn-sm" style="margin-top:.5rem;width:100%;">Comprar</button>
    </a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>