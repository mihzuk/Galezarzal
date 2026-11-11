<?php
// ============================================================
//  GALEZARZAL — Admin: Mensajes de contacto
//  Lista lo que los usuarios envían desde Ayuda y Contacto.
// ============================================================
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/aut_verifica.php';

requiereRol(['Administrador']);

$db  = getDB();
$tab = $_GET['tab'] ?? 'nuevo'; // nuevo | leido | todos
if (!in_array($tab, ['nuevo', 'leido', 'todos'], true)) $tab = 'nuevo';

// Marcar como leído / nuevo, o eliminar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idMensaje'], $_POST['accion'])) {
    $id     = (int)$_POST['idMensaje'];
    $accion = $_POST['accion'];
    if ($accion === 'leido') $db->prepare("UPDATE mensajecontacto SET estado='leido' WHERE idMensaje=?")->execute([$id]);
    if ($accion === 'nuevo') $db->prepare("UPDATE mensajecontacto SET estado='nuevo' WHERE idMensaje=?")->execute([$id]);
    if ($accion === 'eliminar') $db->prepare("DELETE FROM mensajecontacto WHERE idMensaje=?")->execute([$id]);
    header('Location: /galezarzal/admin/mensajesContacto.php?tab=' . $tab);
    exit;
}

// Contadores para las pestañas
$cont = ['nuevo' => 0, 'leido' => 0];
foreach ($db->query("SELECT estado, COUNT(*) AS n FROM mensajecontacto GROUP BY estado") as $r) {
    $cont[$r['estado']] = (int)$r['n'];
}
$cont['todos'] = $cont['nuevo'] + $cont['leido'];

$sql = "SELECT m.*, u.username
        FROM mensajecontacto m
        LEFT JOIN usuario u ON u.iduser = m.iduser";
$params = [];
if ($tab !== 'todos') { $sql .= " WHERE m.estado = ?"; $params[] = $tab; }
$sql .= " ORDER BY m.fecha DESC LIMIT 100";
$st = $db->prepare($sql);
$st->execute($params);
$mensajes = $st->fetchAll();

$etiquetas = ['nuevo' => 'Nuevos', 'leido' => 'Leídos', 'todos' => 'Todos'];

$paginaActiva  = 'configuracion';
$tituloPagina  = 'Mensajes de contacto';
$seccionActiva = 'admin-mensajes';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="config-layout">
  <?php require_once __DIR__ . '/../configuracion/sidebar.php'; ?>
  <main class="config-main">
    <div class="config-panel">
      <div class="flex-between mb-2">
        <h2>Mensajes de contacto</h2>
        <span style="font-size:.85rem;color:var(--gris);"><?= count($mensajes) ?> mostrados</span>
      </div>

      <div style="display:flex;gap:.5rem;margin-bottom:1.25rem;flex-wrap:wrap;">
        <?php foreach ($etiquetas as $k => $v): ?>
        <a href="?tab=<?= $k ?>" class="filtro-btn <?= $tab === $k ? 'activo' : '' ?>"><?= $v ?> (<?= $cont[$k] ?>)</a>
        <?php endforeach; ?>
      </div>

      <?php if (empty($mensajes)): ?>
        <div class="alerta alerta-info">No hay mensajes en esta categoría.</div>
      <?php else: ?>
      <div class="tabla-wrap">
        <table>
          <thead>
            <tr><th>Fecha</th><th>De</th><th>Asunto y mensaje</th><th>Estado</th><th>Acciones</th></tr>
          </thead>
          <tbody>
          <?php foreach ($mensajes as $m): ?>
          <tr>
            <td style="font-size:.8rem;color:var(--gris);white-space:nowrap;">
              <?= date('d/m/Y', strtotime($m['fecha'])) ?><br><?= date('H:i', strtotime($m['fecha'])) ?>
            </td>
            <td style="font-size:.85rem;">
              <strong><?= htmlspecialchars($m['nombre']) ?></strong><br>
              <a href="mailto:<?= htmlspecialchars($m['email']) ?>"><?= htmlspecialchars($m['email']) ?></a>
              <?php if ($m['username']): ?>
                <br><small style="color:var(--gris);">Usuario: <?= htmlspecialchars($m['username']) ?></small>
              <?php endif; ?>
            </td>
            <td style="font-size:.85rem;max-width:380px;">
              <strong><?= htmlspecialchars($m['asunto'] ?: '(sin asunto)') ?></strong>
              <div style="margin-top:.25rem;line-height:1.5;"><?= nl2br(htmlspecialchars($m['mensaje'])) ?></div>
            </td>
            <td>
              <span class="card-badge badge-<?= $m['estado'] === 'nuevo' ? 'pausado' : 'activo' ?>">
                <?= strtoupper($m['estado'] === 'nuevo' ? 'nuevo' : 'leído') ?>
              </span>
            </td>
            <td>
              <form method="POST" style="display:flex;gap:.4rem;flex-wrap:wrap;"
                    onsubmit="return event.submitter && event.submitter.value !== 'eliminar' || confirm('¿Eliminar este mensaje?');">
                <input type="hidden" name="idMensaje" value="<?= (int)$m['idMensaje'] ?>">
                <?php if ($m['estado'] === 'nuevo'): ?>
                  <button name="accion" value="leido" class="btn-outline btn-sm">Marcar leído</button>
                <?php else: ?>
                  <button name="accion" value="nuevo" class="btn-gris btn-sm">Marcar nuevo</button>
                <?php endif; ?>
                <button name="accion" value="eliminar" class="btn-gris btn-sm">Eliminar</button>
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
