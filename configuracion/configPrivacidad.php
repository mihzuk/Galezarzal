
<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/aut_verifica.php';
requiereLogin();

$iduser = (int) $_SESSION['iduser'];
$db = getDB();

$exito = '';
$error = '';

$opciones = [
    'priv_nombre_completo',
    'priv_resenas_visibles',
    'priv_visible_vendedores'
];

// Valores iniciales
$preferencias = array_fill_keys($opciones, 1);

// Consultar preferencias existentes
$consulta = $db->prepare(
    'SELECT priv_nombre_completo, priv_resenas_visibles,
            priv_visible_vendedores
     FROM preferencias_usuario
     WHERE iduser = ?'
);
$consulta->execute([$iduser]);
$guardadas = $consulta->fetch(PDO::FETCH_ASSOC);

if ($guardadas) {
    $preferencias = $guardadas;
}

// Guardar preferencias
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($opciones as $opcion) {
        $preferencias[$opcion] = isset($_POST[$opcion]) ? 1 : 0;
    }

    try {
        $guardar = $db->prepare(
            'INSERT INTO preferencias_usuario
                (iduser, priv_nombre_completo,
                 priv_resenas_visibles, priv_visible_vendedores)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                priv_nombre_completo = VALUES(priv_nombre_completo),
                priv_resenas_visibles = VALUES(priv_resenas_visibles),
                priv_visible_vendedores = VALUES(priv_visible_vendedores)'
        );

        $guardar->execute([
            $iduser,
            $preferencias['priv_nombre_completo'],
            $preferencias['priv_resenas_visibles'],
            $preferencias['priv_visible_vendedores']
        ]);

        $exito = 'Configuración de privacidad guardada.';
    } catch (PDOException $e) {
        error_log($e->getMessage());
        $error = 'No se pudo guardar la configuración de privacidad.';
    }
}

$paginaActiva = 'configuracion';
$tituloPagina = 'Privacidad';
$seccionActiva = 'privacidad';

require_once __DIR__ . '/../includes/header.php';
?>

<div class="config-layout">
  <?php require_once __DIR__ . '/sidebar.php'; ?>

  <main class="config-main">
    <div class="config-panel">
      <h2>Privacidad</h2>

      <?php if ($exito): ?>
        <div class="alerta alerta-exito"><?= htmlspecialchars($exito) ?></div>
      <?php endif; ?>

      <?php if ($error): ?>
        <div class="alerta alerta-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST">
        <?php foreach ([
          ['priv_nombre_completo', 'Mostrar nombre completo', 'En reseñas y contactos con vendedores'],
          ['priv_resenas_visibles', 'Permitir reseñas visibles', 'Tus reseñas serán públicas'],
          ['priv_visible_vendedores', 'Visible para vendedores', 'Que puedan ver tu perfil al contactarlos'],
        ] as [$campo, $tit, $desc]): ?>

        <div class="toggle-row">
          <div class="toggle-info">
            <h4><?= htmlspecialchars($tit) ?></h4>
            <p><?= htmlspecialchars($desc) ?></p>
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

        <button type="submit" class="btn-primary" style="margin-top:1.5rem;">
          Guardar
        </button>
      </form>
    </div>
  </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
