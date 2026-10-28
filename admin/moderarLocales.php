<?php
// ============================================================
//  GALEZARZAL — Admin: Moderar locales
// ============================================================
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/aut_verifica.php';

requiereRol(['Administrador']);

$db  = getDB();
$tab = $_GET['tab'] ?? 'revision'; // revision | activo | inactivo

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idLocal'], $_POST['accion'])) {
    $idLocal = (int)$_POST['idLocal'];
    $estado  = match($_POST['accion']) {
        'aprobar'   => 'activo',
        'rechazar'  => 'inactivo',
        'activar'   => 'activo',
        'inactivar' => 'inactivo',
        default     => null,
    };
    if ($estado) $db->prepare("UPDATE Local SET estado=? WHERE idLocal=?")->execute([$estado, $idLocal]);
    header('Location: /galezarzal/admin/moderarLocales.php?tab='.$tab);
    exit;
}

$estados = ['revision'=>'En revisión','activo'=>'Activos','inactivo'=>'Inactivos'];
$st = $db->prepare(
    "SELECT l.*, v.nomVendedor FROM Local l
     JOIN Vendedor v ON v.idVendedor = l.idVendedor
     WHERE l.estado = ?
     ORDER BY l.fechaRegistro DESC"
);
$st->execute([$tab]);
$locales = $st->fetchAll();

$paginaActiva  = 'configuracion';
$tituloPagina  = 'Moderar locales';
$seccionActiva = 'admin-locales';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="config-layout">
  <?php require_once __DIR__ . '/../configuracion/sidebar.php'; ?>
  <main class="config-main">
    <div class="config-panel">
      <h2>Moderar locales</h2>

      <div style="display:flex;gap:.5rem;margin-bottom:1.25rem;flex-wrap:wrap;">
        <?php foreach ($estados as $k => $v): ?>
        <a href="?tab=<?= $k ?>" class="filtro-btn <?= $tab === $k ? 'activo' : '' ?>"><?= $v ?></a>
        <?php endforeach; ?>
      </div>

      <?php if (empty($locales)): ?>
        <div class="alerta alerta-info">No hay locales en esta categoría.</div>
      <?php else: ?>
      <div class="tabla-wrap">
        <table>
          <thead><tr><th>Local</th><th>Vendedor</th><th>Ubicación</th><th>Registro</th><th>Acciones</th></tr></thead>
          <tbody>
          <?php foreach ($locales as $l): ?>
          <tr>
            <td><strong><?= htmlspecialchars($l['nomLocal']) ?></strong><br>
                <small style="color:var(--gris);font-size:.78rem;"><?= htmlspecialchars(substr($l['describLocal'] ?? '', 0, 50)) ?>…</small></td>
            <td style="font-size:.85rem;"><?= htmlspecialchars($l['nomVendedor']) ?></td>
            <td style="font-size:.82rem;color:var(--gris);"><?= htmlspecialchars($l['ubiLocal'] ?? '—') ?></td>
            <td style="font-size:.8rem;color:var(--gris);"><?= date('d/m/Y', strtotime($l['fechaRegistro'] ?? 'now')) ?></td>
            <td>
              <form method="POST" style="display:flex;gap:.4rem;flex-wrap:wrap;">
                <input type="hidden" name="idLocal" value="<?= $l['idLocal'] ?>">
                <?php if ($l['estado'] === 'revision'): ?>
                  <button name="accion" value="aprobar"  class="btn-primary btn-sm">✓ Aprobar</button>
                  <button name="accion" value="rechazar" class="btn-peligro btn-sm">✗ Rechazar</button>
                <?php elseif ($l['estado'] === 'activo'): ?>
                  <a href="/galezarzal/local/verLocal.php?id=<?= $l['idLocal'] ?>" class="btn-outline btn-sm">Ver</a>
                  <button name="accion" value="inactivar" class="btn-gris btn-sm">Pausar</button>
                <?php else: ?>
                  <button name="accion" value="activar" class="btn-outline btn-sm">Reactivar</button>
                <?php endif; ?>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>