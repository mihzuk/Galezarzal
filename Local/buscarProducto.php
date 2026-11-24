<?php
// GALEZARZAL - Búsqueda de productos (Issue #9)
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/aut_verifica.php';

$db = getDB();
$q = trim($_GET['q'] ?? '');

$sql = "SELECT p.idProducto, p.nomProducto, p.precio,
               p.fotosUrl, l.nomLocal
        FROM Producto p
        JOIN Local l ON l.idLocal = p.idLocal
        WHERE p.estado = 'disponible'
          AND l.estado = 'activo'";

$params = [];

if ($q !== '') {
    $sql .= " AND p.nomProducto LIKE ?";
    $params[] = "%$q%";
}

$sql .= " ORDER BY p.nomProducto ASC";

$st = $db->prepare($sql);
$st->execute($params);
$productos = $st->fetchAll();

$paginaActiva = 'locales';
$tituloPagina = 'Buscar productos';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="seccion">
    <h1>Buscar productos</h1>
    <p>Encuentra productos disponibles en Galezarzal.</p>

    <form method="GET" style="margin:1.5rem 0;">
        <div class="buscador" style="max-width:560px;">
            <input type="text" name="q"
                   placeholder="Buscar productos por nombre..."
                   value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit">🔍</button>
        </div>
    </form>

    <?php if (empty($productos)): ?>
        <div class="alerta alerta-info">
            No se encontraron productos.
        </div>
    <?php else: ?>
        <div class="cards-grid">
            <?php foreach ($productos as $p): ?>
                <?php
                $fotos = json_decode($p['fotosUrl'] ?? '[]', true);
                $foto = is_array($fotos) ? ($fotos[0] ?? '') : '';
                ?>

                <a href="/galezarzal/catalogo/catalogo.php?id=<?= (int)$p['idProducto'] ?>"
                   class="card">

                    <?php if ($foto): ?>
                        <img src="<?= htmlspecialchars($foto, ENT_QUOTES, 'UTF-8') ?>"
                             alt="<?= htmlspecialchars($p['nomProducto'], ENT_QUOTES, 'UTF-8') ?>"
                             style="width:100%;height:180px;object-fit:cover;border-radius:6px;">
                    <?php endif; ?>

                    <h3><?= htmlspecialchars($p['nomProducto'], ENT_QUOTES, 'UTF-8') ?></h3>

                    <p>
                        $<?= number_format($p['precio'], 0, ',', '.') ?>
                    </p>

                    <p class="text-gris">
                        <?= htmlspecialchars($p['nomLocal'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
