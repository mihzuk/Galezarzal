<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/aut_verifica.php';
requiereLogin();

$iduser = (int) $_SESSION['iduser'];
$db = getDB();
$exito = '';
$error = '';

$opciones = [
    'notif_productos',
    'notif_respuestas',
    'notif_novedades',
    'notif_correo'
];

$preferencias = array_fill_keys($opciones, 1);

// Consultar las preferencias guardadas
$consulta = $db->prepare(
    'SELECT notif_productos, notif_respuestas,
            notif_novedades, notif_correo
     FROM preferencias_usuario WHERE iduser = ?'
);
$consulta->execute([$iduser]);
$guardadas = $consulta->fetch(PDO::FETCH_ASSOC);

if ($guardadas) {
    $preferencias = $guardadas;
}

// Guardar los cambios del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($opciones as $opcion) {
        $preferencias[$opcion] = isset($_POST[$opcion]) ? 1 : 0;
    }

    try {
        $guardar = $db->prepare(
            'INSERT INTO preferencias_usuario
                (iduser, notif_productos, notif_respuestas,
                 notif_novedades, notif_correo)
             VALUES (?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                notif_productos = VALUES(notif_productos),
                notif_respuestas = VALUES(notif_respuestas),
                notif_novedades = VALUES(notif_novedades),
                notif_correo = VALUES(notif_correo)'
        );

        $guardar->execute([
            $iduser,
            $preferencias['notif_productos'],
            $preferencias['notif_respuestas'],
            $preferencias['notif_novedades'],
            $preferencias['notif_correo']
        ]);

        $exito = 'Preferencias de notificaciones guardadas.';
    } catch (PDOException $e) {
        error_log($e->getMessage());
        $error = 'No se pudieron guardar las preferencias.';
    }
}

$paginaActiva = 'configuracion';
$tituloPagina = 'Notificaciones';
$seccionActiva = 'notificaciones';

require_once __DIR__ . '/../includes/header.php';
?>

<div class="config-layout">
  <?php require_once __DIR__ . '/sidebar.php'; ?>
  <main class="config-main">
    <div class="config-panel">
      <h2>Notificaciones</h2>
<?php if ($exito): ?><div class="alerta alerta-exito"><?= htmlspecialchars($exito) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alerta alerta-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="POST">
        <?php foreach ([
  ['notif_productos', 'Nuevos productos en locales favoritos', 'De locales que sigues'],
  ['notif_respuestas', 'Respuestas de vendedores', 'Cuando un local te responde'],
  ['notif_novedades', 'Novedades de la plataforma', 'Nuevos locales afiliados'],
  ['notif_correo', 'Por correo electrónico', 'Recibir notificaciones también por email'],
] as [$campo, $tit, $desc]): ?>
        <div class="toggle-row">
          <div class="toggle-info">
            <h4><?= $tit ?></h4>
            <p><?= $desc ?></p>
          </div>
          <label class="toggle">
            <input type="checkbox"
       name="<?= htmlspecialchars($campo) ?>"
       value="1"
       <?= !empty($preferencias[$campo]) ? 'checked' : '' ?>>
            <span class="slider"></span>
          </label>
        </div>
        <?php endforeach; ?>
        <button type="submit" class="btn-primary" style="margin-top:1.5rem;">Guardar</button>
      </form>
    </div>
  </main>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
