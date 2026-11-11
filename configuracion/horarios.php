<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/aut_verifica.php';
require_once __DIR__ . '/../includes/aut_visualizar_error.php';
require_once __DIR__ . '/../includes/validaciones.php';
requiereRol(['Vendedor','Administrador']);

$db = getDB();
$exito = ''; $error = '';

// Obtener local + horario
$st = $db->prepare("SELECT l.idLocal, l.idHorario, h.* FROM Local l JOIN Vendedor v ON v.idVendedor=l.idVendedor LEFT JOIN Horario h ON h.idHorario=l.idHorario WHERE v.iduser=? LIMIT 1");
$st->execute([$_SESSION['iduser']]);
$row = $st->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $row) {
    $dias      = diasValidos((array)($_POST['dias'] ?? []));   // solo LUN..DOM
    $apertura  = trim($_POST['apertura']  ?? '08:00');
    $cierre    = trim($_POST['cierre']    ?? '18:00');
    $descanso  = trim($_POST['descanso'] ?? '');

    // Validación en el servidor
    $error = validarHorario($apertura, $cierre, $descanso);

    if ($error === '') {
        if ($row['idHorario']) {
            $db->prepare("UPDATE Horario SET diasDisponibles=?,horarioApertura=?,horarioCierre=?,descansoOpc=? WHERE idHorario=?")
               ->execute([$dias,$apertura,$cierre,$descanso,$row['idHorario']]);
        } else {
            $ins = $db->prepare("INSERT INTO Horario (diasDisponibles,horarioApertura,horarioCierre,descansoOpc) VALUES (?,?,?,?)");
            $ins->execute([$dias,$apertura,$cierre,$descanso]);
            $newId = $db->lastInsertId();
            $db->prepare("UPDATE Local SET idHorario=? WHERE idLocal=?")->execute([$newId, $row['idLocal']]);
        }
        $exito = 'Horarios guardados.';
        $st->execute([$_SESSION['iduser']]);
        $row = $st->fetch();
    } else {
        // Se muestra lo que escribió, para que corrija
        $row = array_merge($row, ['horarioApertura'=>$apertura,'horarioCierre'=>$cierre,'descansoOpc'=>$descanso,'diasDisponibles'=>$dias]);
    }
}

$diasActivos = $row ? array_flip(explode(',', $row['diasDisponibles'] ?? '')) : [];
$diasSemana  = DIAS_SEMANA;

$paginaActiva = 'configuracion'; $tituloPagina = 'Horarios'; $seccionActiva = 'horarios';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="config-layout">
  <?php require_once __DIR__ . '/sidebar.php'; ?>
  <main class="config-main">
    <div class="config-panel">
      <h2>Horarios de atención</h2>
      <?php if ($error): ?><div class="alerta alerta-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
      <?php if ($exito): ?><div class="alerta alerta-exito"><?= htmlspecialchars($exito) ?></div><?php endif; ?>
      <form method="POST">
        <p style="font-weight:600;color:var(--rojo);font-size:.88rem;margin-bottom:.5rem;">Días disponibles</p>
        <div class="dias-grid">
          <?php foreach ($diasSemana as $d): ?>
          <label class="dia-btn <?= isset($diasActivos[$d]) ? 'activo' : '' ?>"
                 style="cursor:pointer;user-select:none;">
            <input type="checkbox" name="dias[<?= $d ?>]" value="1" style="display:none;"
                   <?= isset($diasActivos[$d]) ? 'checked' : '' ?>
                   onchange="this.closest('.dia-btn').classList.toggle('activo',this.checked)">
            <?= $d ?>
          </label>
          <?php endforeach; ?>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Hora de apertura</label><input type="time" name="apertura" required value="<?= htmlspecialchars($row['horarioApertura']??'08:00') ?>"></div>
          <div class="form-group"><label>Hora de cierre</label><input type="time" name="cierre" required value="<?= htmlspecialchars($row['horarioCierre']??'18:00') ?>"></div>
        </div>
        <div class="form-group"><label>Descanso (opcional)</label><input type="text" name="descanso" placeholder="Ej: 12:30 - 2:00 pm" maxlength="50" value="<?= htmlspecialchars($row['descansoOpc']??'') ?>"></div>
        <button type="submit" class="btn-primary">Guardar horarios</button>
      </form>
    </div>
  </main>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>