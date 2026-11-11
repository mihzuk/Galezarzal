<?php
// ============================================================
//  GALEZARZAL — Favoritos del usuario
// ============================================================
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/aut_verifica.php';
require_once __DIR__ . '/../includes/aut_visualizar_error.php';

requiereLogin();

$db = getDB();
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

// Eliminar favorito
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quitar'])) {
    $idLocal = (int)$_POST['idLocal'];
    $db->prepare("DELETE FROM Favorito WHERE iduser=? AND idLocal=?")->execute([$_SESSION['iduser'], $idLocal]);
    header('Location: /galezarzal/favoritos/consultarFavoritos.php');
    exit;
}

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

      <?php if (empty($favoritos)): ?>
        <div class="alerta alerta-info">
          Aún no tienes locales guardados como favoritos.
          <a href="/galezarzal/local/buscarLocal.php" style="color:var(--rojo);">Explorar locales</a>
        </div>
      <?php else: ?>
      <div class="cards-grid" style="margin-top:1rem;">
        <?php foreach ($favoritos as $loc): ?>
        <div class="card" style="position:relative;">
          <span class="card-badge badge-mixto"><?= htmlspecialchars($loc['catPrincipal'] ?? 'Mixto') ?></span>
          <a href="/galezarzal/local/verLocal.php?id=<?= $loc['idLocal'] ?>" style="display:block;text-decoration:none;color:inherit;">
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
    </div>
  </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
