<?php
// ============================================================
//  GALEZARZAL — Notificaciones del usuario
// ============================================================
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/aut_verifica.php';

requiereLogin();

$db = getDB();

// Marcar como leída
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['marcarTodas'])) {
        $db->prepare("UPDATE Notificacion SET estado='leida' WHERE iduser=?")->execute([$_SESSION['iduser']]);
    } elseif (isset($_POST['idNotif'])) {
        $db->prepare("UPDATE Notificacion SET estado='leida' WHERE idNotificacion=? AND iduser=?")
           ->execute([(int)$_POST['idNotif'], $_SESSION['iduser']]);
    }
    header('Location: /galezarzal/notificaciones/notificaciones.php');
    exit;
}

// Cargar notificaciones
$notifs = [];
try {
    $st = $db->prepare(
        "SELECT * FROM Notificacion WHERE iduser=? ORDER BY fecha DESC LIMIT 50"
    );
    $st->execute([$_SESSION['iduser']]);
    $notifs = $st->fetchAll();
} catch(Exception $e) { /* tabla puede no existir */ }

$noLeidas = count(array_filter($notifs, fn($n) => $n['estado'] === 'no_leida'));

$paginaActiva = '';
$tituloPagina = 'Notificaciones';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="seccion" style="max-width:720px;">
  <div class="flex-between mb-2">
    <h1 style="font-size:1.3rem;font-weight:700;">
      Notificaciones <?php if ($noLeidas): ?><sup style="font-size:.7rem;background:var(--rojo);color:#fff;border-radius:10px;padding:1px 6px;"><?= $noLeidas ?></sup><?php endif; ?>
    </h1>
    <?php if ($noLeidas): ?>
    <form method="POST">
      <button name="marcarTodas" value="1" class="btn-gris btn-sm">Marcar todo leído</button>
    </form>
    <?php endif; ?>
  </div>

  <?php if (empty($notifs)): ?>
    <div class="alerta alerta-info">No tienes notificaciones todavía.</div>
  <?php else: ?>
  <div style="background:var(--blanco);border-radius:var(--radio);box-shadow:var(--sombra);overflow:hidden;">
    <?php foreach ($notifs as $n): ?>
    <?php $leida = $n['estado'] !== 'no_leida'; ?>
    <div style="display:flex;align-items:center;justify-content:space-between;padding:1rem 1.25rem;
                border-bottom:1px solid #eee;<?= !$leida ? 'border-left:3px solid var(--rojo);background:var(--crema);' : '' ?>">
      <div style="flex:1;">
        <p style="font-size:.9rem;<?= $leida ? '' : 'font-weight:600;' ?>"><?= htmlspecialchars($n['mensaje']) ?></p>
        <p style="font-size:.78rem;color:var(--gris);margin-top:.2rem;">
          <?= date('d/m/Y H:i', strtotime($n['fecha'] ?? 'now')) ?>
        </p>
      </div>
      <div style="display:flex;align-items:center;gap:.75rem;margin-left:1rem;">
        <?php if (!empty($n['urlDestino'])): ?>
          <a href="<?= htmlspecialchars($n['urlDestino']) ?>" class="notif-ver">Ver</a>
        <?php endif; ?>
        <?php if (!$leida): ?>
        <form method="POST">
          <input type="hidden" name="idNotif" value="<?= $n['idNotificacion'] ?>">
          <button type="submit" class="btn-gris btn-sm" style="font-size:.75rem;">✓</button>
        </form>
        <?php else: ?>
          <span style="color:var(--gris);font-size:.75rem;">Leída</span>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
