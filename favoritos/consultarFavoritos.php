<?php
// ============================================================
//  GALEZARZAL — Favoritos del usuario
// ============================================================
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/aut_verifica.php';
require_once __DIR__ . '/../includes/aut_visualizar_error.php';

requiereLogin();

$db = getDB();

// Quitar un local de favoritos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quitar'])) {
    $idLocal = (int)$_POST['idLocal'];
    $db->prepare("DELETE FROM Favorito WHERE iduser=? AND idLocal=?")->execute([$_SESSION['iduser'], $idLocal]);
    header('Location: ' . BASE_PATH . '/favoritos/consultarFavoritos.php');
    exit;
}

// Quitar un producto de favoritos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quitarProducto'])) {
    $idProd = (int)$_POST['idProducto'];
    $db->prepare("DELETE FROM Favorito WHERE iduser=? AND idProducto=?")->execute([$_SESSION['iduser'], $idProd]);
    header('Location: ' . BASE_PATH . '/favoritos/consultarFavoritos.php');
    exit;
}

// Locales favoritos
$st = $db->prepare(
    "SELECT l.idLocal, l.nomLocal, l.describLocal, l.ubiLocal, l.urlWhatsapp,
            (SELECT cat.nomCategoria FROM Producto p
             JOIN Categoria cat ON cat.idcategoria=p.idcategoria
             WHERE p.idLocal=l.idLocal LIMIT 1) AS catPrincipal
     FROM Favorito f
     JOIN Local l ON l.idLocal = f.idLocal
     WHERE f.iduser = ? AND l.estado = 'activo'
     ORDER BY f.idFavorito DESC"
);
$st->execute([$_SESSION['iduser']]);
$favoritos = $st->fetchAll();

// Productos favoritos
$stProd = $db->prepare(
    "SELECT p.idProducto, p.nomProducto, p.precio, p.fotosUrl, p.estado, l.nomLocal
     FROM Favorito f
     JOIN Producto p ON p.idProducto = f.idProducto
     JOIN Local l ON l.idLocal = p.idLocal
     WHERE f.iduser = ? AND p.estado != 'inactivo' AND l.estado = 'activo'
     ORDER BY f.idFavorito DESC"
);
$stProd->execute([$_SESSION['iduser']]);
$favProductos = $stProd->fetchAll();

$paginaActiva   = 'configuracion';
$tituloPagina   = 'Favoritos';
$seccionActiva  = 'favoritos';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="config-layout">
  <?php require_once __DIR__ . '/../configuracion/sidebar.php'; ?>
  <main class="config-main">
    <div class="config-panel">
      <h2>Mis favoritos</h2>

      <?php if (empty($favoritos) && empty($favProductos)): ?>
        <div class="alerta alerta-info">
          Aún no tienes favoritos guardados.
          <a href="<?= htmlspecialchars(BASE_PATH, ENT_QUOTES, 'UTF-8') ?>/local/buscarLocal.php" style="color:var(--rojo);">Explorar locales</a>
        </div>
      <?php else: ?>

      <?php if (!empty($favoritos)): ?>
      <h3 style="font-size:1rem;font-weight:700;margin:1rem 0 .75rem;">Locales favoritos</h3>
      <div class="cards-grid">
        <?php foreach ($favoritos as $loc): ?>
        <div class="card" style="position:relative;">
          <span class="card-badge badge-mixto"><?= htmlspecialchars($loc['catPrincipal'] ?? 'Mixto') ?></span>
          <a href="<?= htmlspecialchars(BASE_PATH, ENT_QUOTES, 'UTF-8') ?>/local/verLocal.php?id=<?= $loc['idLocal'] ?>" style="display:block;text-decoration:none;color:inherit;">
            <h3><?= htmlspecialchars($loc['nomLocal']) ?></h3>
            <p style="margin:.3rem 0;font-size:.82rem;color:var(--gris);">
              <?= htmlspecialchars(substr($loc['describLocal'] ?? '', 0, 60)) ?>…
            </p>
            <p style="font-size:.78rem;color:var(--gris);">📍 <?= htmlspecialchars($loc['ubiLocal'] ?? '') ?></p>
          </a>
          <div style="display:flex;gap:.5rem;margin-top:.75rem;flex-wrap:wrap;">
            <a href="https://wa.me/<?= htmlspecialchars($loc['urlWhatsapp']) ?>"
               target="_blank" class="btn-outline btn-sm" style="font-size:.75rem;">💬 WhatsApp</a>
            <form method="POST" style="display:inline;">
              <input type="hidden" name="idLocal" value="<?= $loc['idLocal'] ?>">
              <button type="submit" name="quitar" value="1"
                      class="btn-gris btn-sm" style="font-size:.75rem;"
                      onclick="return confirm('¿Quitar de favoritos?')">❌ Quitar</button>
            </form>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <?php if (!empty($favProductos)): ?>
      <h3 style="font-size:1rem;font-weight:700;margin:1.5rem 0 .75rem;">Productos favoritos</h3>
      <div class="cards-grid">
        <?php foreach ($favProductos as $p): ?>
        <?php $pf = json_decode($p['fotosUrl'] ?? '[]', true) ?: []; ?>
        <div class="card">
          <a href="<?= htmlspecialchars(BASE_PATH, ENT_QUOTES, 'UTF-8') ?>/catalogo/catalogo.php?id=<?= (int)$p['idProducto'] ?>" style="display:block;color:inherit;">
            <?php if (!empty($pf[0])): ?>
              <img src="<?= htmlspecialchars($pf[0]) ?>" alt="" style="width:100%;height:120px;object-fit:cover;border-radius:6px;margin-bottom:.5rem;">
            <?php endif; ?>
            <h3 style="font-size:.95rem;"><?= htmlspecialchars($p['nomProducto']) ?></h3>
            <p style="color:var(--rojo);font-weight:700;">$<?= number_format($p['precio'], 0, ',', '.') ?></p>
            <p style="font-size:.78rem;color:var(--gris);"><?= htmlspecialchars($p['nomLocal']) ?></p>
          </a>
          <form method="POST" style="margin-top:.75rem;" onsubmit="return confirm('¿Quitar de favoritos?');">
            <input type="hidden" name="idProducto" value="<?= (int)$p['idProducto'] ?>">
            <button type="submit" name="quitarProducto" value="1" class="btn-gris btn-sm">Quitar</button>
          </form>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <?php endif; ?>
    </div>
  </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>