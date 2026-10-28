<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/aut_verifica.php';

$db      = getDB();
$q       = trim($_GET['q']    ?? '');
$cat     = trim($_GET['cat']  ?? '');
$verFav  = ($_GET['fav'] ?? '') === '1';
$pagina  = max(1, (int)($_GET['p'] ?? 1));
$porPag  = 9;
$offset  = ($pagina - 1) * $porPag;

$where  = ["l.estado = 'activo'"];
$params = [];

if ($q !== '') {
    $where[]  = "(l.nomLocal LIKE ? OR l.describLocal LIKE ?)";
    $params[] = "%$q%";
    $params[] = "%$q%";
}

$categorias = $db->query("SELECT * FROM Categoria ORDER BY nomCategoria")->fetchAll();

// Filtro por categoría
$catNom = '';
foreach ($categorias as $c) {
    if ((string)$c['idcategoria'] === $cat) { $catNom = $c['nomCategoria']; break; }
}
if ($catNom) {
    $where[]  = "EXISTS (SELECT 1 FROM Producto p JOIN Categoria cat ON cat.idcategoria=p.idcategoria WHERE p.idLocal=l.idLocal AND cat.nomCategoria LIKE ?)";
    $params[] = "%$catNom%";
}

// Filtro favoritos (solo usuarios logueados)
if ($verFav && sesionActiva()) {
    $where[]  = "EXISTS (SELECT 1 FROM Favorito f WHERE f.idLocal=l.idLocal AND f.iduser=?)";
    $params[] = $_SESSION['iduser'];
}

$whereStr = 'WHERE ' . implode(' AND ', $where);

$stCount = $db->prepare("SELECT COUNT(*) FROM Local l $whereStr");
$stCount->execute($params);
$total    = (int)$stCount->fetchColumn();
$totalPag = (int)ceil($total / $porPag);

$stLocales = $db->prepare(
    "SELECT l.*, v.nomVendedor,
            (SELECT cat.nomCategoria FROM Producto p JOIN Categoria cat ON cat.idcategoria=p.idcategoria WHERE p.idLocal=l.idLocal LIMIT 1) AS catPrincipal
     FROM Local l
     JOIN Vendedor v ON v.idVendedor = l.idVendedor
     $whereStr
     ORDER BY l.fechaRegistro DESC
     LIMIT $porPag OFFSET $offset"
);
$stLocales->execute($params);
$locales = $stLocales->fetchAll();

// Mapa de etiquetas cortas para categorías
$labelCorto = function(string $c): string {
    $c = strtolower($c);
    if (str_contains($c, 'mujer'))   return 'Mujer';
    if (str_contains($c, 'hombre'))  return 'Hombre';
    if (str_contains($c, 'niño') || str_contains($c, 'nino') || str_contains($c, 'bebe') || str_contains($c, 'bebé')) return 'Niños';
    if (str_contains($c, 'acceso'))  return 'Accesorios';
    if (str_contains($c, 'deport'))  return 'Deportivo';
    if (str_contains($c, 'calzado')) return 'Calzado';
    return ucfirst($c);
};

$badgeCat = function(string $c): string {
    $mapa = ['mujer'=>'mujer','hombre'=>'hombre','niño'=>'ninos','nino'=>'ninos','acceso'=>'acces','mixto'=>'mixto','deport'=>'mixto','calzado'=>'mixto'];
    $cl = strtolower($c);
    foreach ($mapa as $k => $v) { if (str_contains($cl, $k)) return $v; }
    return 'mixto';
};

$paginaActiva = 'locales'; $tituloPagina = 'Locales';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="hero" style="padding:2rem;">
  <h1 style="font-size:1.6rem;">Encuentra un local</h1>
  <p style="color:#aaa;font-size:.9rem;">Ropa de mujer, hombre, niños y accesorios en Zarzal</p>
  <form method="GET" style="margin-top:1rem;">
    <div class="buscador" style="max-width:560px;">
      <input type="text" name="q" placeholder="Buscar locales, productos o categorías..." value="<?= htmlspecialchars($q) ?>">
      <?php if ($cat): ?><input type="hidden" name="cat" value="<?= htmlspecialchars($cat) ?>"><?php endif; ?>
      <button type="submit">🔍</button>
    </div>
  </form>
</section>

<section class="seccion">
  <!-- Filtros por categoría -->
  <div class="filtros">
    <a href="/galezarzal/local/buscarLocal.php<?= $q ? '?q='.urlencode($q) : '' ?>"
       class="filtro-btn <?= (!$cat && !$verFav) ? 'activo' : '' ?>">Todos</a>
    <?php foreach ($categorias as $c): ?>
    <a href="?cat=<?= $c['idcategoria'] ?><?= $q ? '&q='.urlencode($q) : '' ?>"
       class="filtro-btn <?= $cat==(string)$c['idcategoria'] ? 'activo' : '' ?>">
      <?= htmlspecialchars($labelCorto($c['nomCategoria'])) ?>
    </a>
    <?php endforeach; ?>
    <?php if (sesionActiva()): ?>
    <a href="?fav=1<?= $q ? '&q='.urlencode($q) : '' ?>"
       class="filtro-btn <?= $verFav ? 'activo' : '' ?>">❤ Favoritos</a>
    <?php endif; ?>
  </div>

  <?php if (empty($locales)): ?>
  <div class="alerta alerta-info">
    <?= $verFav ? 'Aún no tienes locales favoritos. <a href="/galezarzal/local/buscarLocal.php" style="color:var(--rojo);">Explorar locales</a>' : 'No se encontraron locales con esos criterios.' ?>
  </div>
  <?php else: ?>
  <div class="cards-grid">
    <?php foreach ($locales as $l): ?>
    <a href="/galezarzal/local/verLocal.php?id=<?= $l['idLocal'] ?>" class="card">
      <span class="card-badge badge-<?= $badgeCat($l['catPrincipal']??'mixto') ?>">
        <?= htmlspecialchars($labelCorto($l['catPrincipal']??'Mixto')) ?>
      </span>
      <h3><?= htmlspecialchars($l['nomLocal']) ?></h3>
      <p><?= htmlspecialchars(substr($l['describLocal']??'',0,70)) ?><?= strlen($l['describLocal']??'')>70?'…':'' ?></p>
      <?php if ($l['ubiLocal']): ?>
      <p style="margin-top:.5rem;font-size:.8rem;color:var(--gris);">📍 <?= htmlspecialchars($l['ubiLocal']) ?></p>
      <?php endif; ?>
    </a>
    <?php endforeach; ?>
  </div>

  <!-- Paginación -->
  <?php if ($totalPag > 1): ?>
  <div style="display:flex;gap:.5rem;justify-content:center;margin-top:2rem;">
    <?php for($i=1;$i<=$totalPag;$i++): ?>
    <a href="?p=<?= $i ?><?= $q ? '&q='.urlencode($q) : '' ?><?= $cat ? '&cat='.$cat : '' ?><?= $verFav ? '&fav=1' : '' ?>"
       class="filtro-btn <?= $i===$pagina ? 'activo' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
  </div>
  <?php endif; ?>
  <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
