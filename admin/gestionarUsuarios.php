<?php
// ============================================================
//  GALEZARZAL — Admin: Gestión de usuarios
// ============================================================
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/aut_verifica.php';

requiereRol(['Administrador']);

$db = getDB();
$q  = trim($_GET['q'] ?? '');

// Cambiar estado de usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['iduser'], $_POST['accion'])) {
    $iduser = (int)$_POST['iduser'];
    $accion = $_POST['accion'];
    if ($iduser !== (int)$_SESSION['iduser']) { // No auto-bloquear
        if ($accion === 'activar')   $db->prepare("UPDATE Usuario SET estado='activo'   WHERE iduser=?")->execute([$iduser]);
        if ($accion === 'inactivar') $db->prepare("UPDATE Usuario SET estado='inactivo' WHERE iduser=?")->execute([$iduser]);
        if ($accion === 'vendedor') {
            $idRolV = $db->query("SELECT idRol FROM Rol WHERE nomRol='Vendedor' LIMIT 1")->fetchColumn();
            if ($idRolV) $db->prepare("UPDATE Usuario SET idRol=? WHERE iduser=?")->execute([$idRolV, $iduser]);
        }
    }
    header('Location: /galezarzal/admin/gestionarUsuarios.php' . ($q ? '?q='.urlencode($q) : ''));
    exit;
}

$where = $q ? "WHERE u.primerNombre LIKE ? OR u.email LIKE ? OR u.username LIKE ?" : "";
$params = $q ? ["%$q%", "%$q%", "%$q%"] : [];
$st = $db->prepare(
    "SELECT u.*, r.nomRol FROM Usuario u
     JOIN Rol r ON r.idRol = u.idRol
     $where
     ORDER BY u.fechaRegistro DESC LIMIT 100"
);
$st->execute($params);
$usuarios = $st->fetchAll();

$paginaActiva  = 'configuracion';
$tituloPagina  = 'Gestión de usuarios';
$seccionActiva = 'admin-usuarios';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="config-layout">
  <?php require_once __DIR__ . '/../configuracion/sidebar.php'; ?>
  <main class="config-main">
    <div class="config-panel">
      <div class="flex-between mb-2">
        <h2>Gestión de usuarios</h2>
        <span style="font-size:.85rem;color:var(--gris);"><?= count($usuarios) ?> resultados</span>
      </div>

      <form method="GET" style="margin-bottom:1.25rem;">
        <div class="buscador" style="max-width:400px;">
          <input type="text" name="q" placeholder="Buscar por nombre, email o usuario…"
                 value="<?= htmlspecialchars($q) ?>">
          <button type="submit">🔍</button>
        </div>
      </form>

      <div class="tabla-wrap">
        <table>
          <thead>
            <tr><th>Usuario</th><th>Email</th><th>Rol</th><th>Estado</th><th>Registro</th><th>Acciones</th></tr>
          </thead>
          <tbody>
          <?php foreach ($usuarios as $u): ?>
          <tr>
            <td><strong><?= htmlspecialchars($u['username']) ?></strong><br>
                <small style="color:var(--gris);"><?= htmlspecialchars($u['primerNombre'].' '.$u['primerApellido']) ?></small></td>
            <td style="font-size:.83rem;"><?= htmlspecialchars($u['email']) ?></td>
            <td><span class="card-badge badge-hombre" style="font-size:.7rem;"><?= htmlspecialchars($u['nomRol']) ?></span></td>
            <td><span class="card-badge badge-<?= $u['estado']==='activo' ? 'activo' : 'agotado' ?>">
              <?= strtoupper($u['estado']) ?></span></td>
            <td style="font-size:.8rem;color:var(--gris);"><?= date('d/m/Y', strtotime($u['fechaRegistro'] ?? 'now')) ?></td>
            <td>
              <?php if ((int)$u['iduser'] !== (int)$_SESSION['iduser']): ?>
              <form method="POST" style="display:flex;gap:.4rem;flex-wrap:wrap;">
                <input type="hidden" name="iduser" value="<?= $u['iduser'] ?>">
                <?php if ($u['estado'] === 'activo'): ?>
                  <button name="accion" value="inactivar" class="btn-gris btn-sm">Desactivar</button>
                <?php else: ?>
                  <button name="accion" value="activar" class="btn-outline btn-sm">Activar</button>
                <?php endif; ?>
                <?php if ($u['nomRol'] === 'Comprador'): ?>
                  <button name="accion" value="vendedor" class="btn-outline btn-sm">→ Vendedor</button>
                <?php endif; ?>
              </form>
              <?php else: ?>
                <span style="color:var(--gris);font-size:.78rem;">Tú</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
