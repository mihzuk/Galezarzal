<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/aut_verifica.php';
require_once __DIR__ . '/../includes/aut_visualizar_error.php';
require_once __DIR__ . '/../includes/validaciones.php';
requiereRol(['Vendedor','Administrador']);

$db     = getDB();
$error  = ''; $exito  = '';

// Obtener local del vendedor
$st = $db->prepare(
    "SELECT l.*, cat.nomCategoria
     FROM Local l
     JOIN Vendedor v ON v.idVendedor = l.idVendedor
     LEFT JOIN (SELECT p.idLocal, MIN(cat.nomCategoria) AS nomCategoria FROM Producto p JOIN Categoria cat ON cat.idcategoria=p.idcategoria GROUP BY p.idLocal) cat ON cat.idLocal = l.idLocal
     WHERE v.iduser = ? LIMIT 1"
);
$st->execute([$_SESSION['iduser']]);
$local = $st->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $local) {
    $nomLocal    = trim($_POST['nomLocal']    ?? '');
    $describLocal= trim($_POST['describLocal']?? '');
    $ubiLocal    = trim($_POST['ubiLocal']    ?? '');
    $urlWA       = trim($_POST['urlWhatsapp'] ?? '');

    // Validación en el servidor
    $error = validarDatosLocal([
        'nomLocal'     => $nomLocal,
        'describLocal' => $describLocal,
        'ubiLocal'     => $ubiLocal,
        'urlWhatsapp'  => $urlWA,
    ]);

    if ($error === '') {
        // El WhatsApp se guarda como 57 + 10 dígitos, listo para wa.me
        $urlWA = formatoWhatsapp($urlWA);
        $db->prepare("UPDATE Local SET nomLocal=?,describLocal=?,ubiLocal=?,urlWhatsapp=? WHERE idLocal=?")
           ->execute([$nomLocal,$describLocal,$ubiLocal,$urlWA,$local['idLocal']]);
        $exito = 'Información actualizada.';
    }
    // Con o sin error, el formulario muestra lo que escribió
    $local = array_merge($local, ['nomLocal'=>$nomLocal,'describLocal'=>$describLocal,'ubiLocal'=>$ubiLocal,'urlWhatsapp'=>$urlWA]);
}

// Categorías para select
$categorias = $db->query("SELECT * FROM Categoria ORDER BY nomCategoria")->fetchAll();

$paginaActiva = 'configuracion'; $tituloPagina = 'Información del local'; $seccionActiva = 'info-local';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="config-layout">
  <?php require_once __DIR__ . '/sidebar.php'; ?>
  <main class="config-main">
    <div class="config-panel">
      <h2>Información del local</h2>
      <?php if ($error): ?><div class="alerta alerta-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
      <?php if ($exito): ?><div class="alerta alerta-exito"><?= htmlspecialchars($exito) ?></div><?php endif; ?>
      <?php if (!$local): ?>
        <div class="alerta alerta-info">No tienes un local registrado aún. <a href="/galezarzal/local/crearLocal.php" style="color:var(--rojo);">Registrar local</a></div>
      <?php else: ?>
      <form method="POST">
        <div class="form-group"><label>Nombre del local</label><input type="text" name="nomLocal" value="<?= htmlspecialchars($local['nomLocal']) ?>" required minlength="3" maxlength="100"></div>
        <div class="form-group"><label>Categoría</label>
          <select name="idcategoria">
            <?php foreach ($categorias as $c): ?>
            <option value="<?= $c['idcategoria'] ?>"><?= htmlspecialchars($c['nomCategoria']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group"><label>Descripción</label><textarea name="describLocal" maxlength="300"><?= htmlspecialchars($local['describLocal']??'') ?></textarea></div>
        <div class="form-group"><label>Número de local en la galería</label><input type="text" name="ubiLocal" value="<?= htmlspecialchars($local['ubiLocal']??'') ?>" placeholder="Local 12, Pasillo B" maxlength="150"></div>
        <div class="form-group"><label>Teléfono / WhatsApp</label><input type="tel" name="urlWhatsapp" value="<?= htmlspecialchars($local['urlWhatsapp']??'') ?>" placeholder="+57 300 123 4567" maxlength="20" required
             pattern="(\+?57[\s\-]?)?3\d{2}[\s\-]?\d{3}[\s\-]?\d{4}"
             title="Celular colombiano de 10 dígitos que empiece por 3. Ej: 300 123 4567"></div>
        <div class="form-acciones">
          <button type="submit" class="btn-primary">Guardar cambios</button>
          <a href="/galezarzal/local/verLocal.php?id=<?= $local['idLocal'] ?>" class="btn-outline">Ver mi local público</a>
        </div>
      </form>
      <?php endif; ?>
    </div>
  </main>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>