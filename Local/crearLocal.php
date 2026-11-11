<?php
// ============================================================
//  GALEZARZAL — Registrar local (wizard 4 pasos)
// ============================================================
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/aut_verifica.php';
require_once __DIR__ . '/../includes/aut_visualizar_error.php';
require_once __DIR__ . '/../includes/validaciones.php';
requiereLogin();

if (session_status() === PHP_SESSION_NONE) session_start();

$paso   = (int)($_POST['paso'] ?? $_GET['paso'] ?? 1);
if ($paso < 1 || $paso > 4) $paso = 1;
$error  = '';
$db     = getDB();

// ── Verificar que no tenga ya un local ──
$stVend = $db->prepare("SELECT v.idVendedor FROM Vendedor v WHERE v.iduser=?");
$stVend->execute([$_SESSION['iduser']]);
$vendedor = $stVend->fetch();

if ($vendedor) {
    $stL = $db->prepare("SELECT idLocal FROM Local WHERE idVendedor=? LIMIT 1");
    $stL->execute([$vendedor['idVendedor']]);
    if ($stL->fetch()) {
        header('Location: /galezarzal/local/miLocal.php');
        exit;
    }
}

$categorias = $db->query("SELECT * FROM Categoria ORDER BY nomCategoria")->fetchAll();

// ── Paso 1 → validar y guardar en sesión ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $paso === 1) {
    $nomLocal     = trim($_POST['nomLocal']     ?? '');
    $idcategoria  = (int)($_POST['idcategoria'] ?? 0);
    $describLocal = trim($_POST['describLocal'] ?? '');
    $ubiLocal     = trim($_POST['ubiLocal']     ?? '');
    $urlWA        = trim($_POST['urlWhatsapp']  ?? '');

    // Se guarda lo escrito para que no lo pierda si hay un error
    $_SESSION['local_wiz'] = compact('nomLocal','idcategoria','describLocal','ubiLocal','urlWA');

    $error = validarDatosLocal([
        'nomLocal'     => $nomLocal,
        'describLocal' => $describLocal,
        'ubiLocal'     => $ubiLocal,
        'urlWhatsapp'  => $urlWA,
    ]) ?: validarCategoria($idcategoria, $categorias);

    if ($error === '') {
        header('Location: /galezarzal/local/crearLocal.php?paso=2');
        exit;
    }
    $paso = 1;
}

// ── Paso 2 → validar y guardar productos temporales ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $paso === 2) {
    $nombres  = (array)($_POST['nomProducto'] ?? []);
    $precios  = (array)($_POST['precio']      ?? []);
    $tallas   = (array)($_POST['talla']       ?? []);
    $seccions = (array)($_POST['seccion']     ?? []);
    $prods = [];
    foreach ($nombres as $i => $nom) {
        if (!is_string($nom) || trim($nom) === '') continue;
        $prods[] = [
            'nom'     => trim($nom),
            'precio'  => trim((string)($precios[$i]  ?? '')),
            'talla'   => trim((string)($tallas[$i]   ?? '')),
            'seccion' => trim((string)($seccions[$i] ?? '')),
        ];
    }
    $_SESSION['local_wiz_prod'] = $prods;

    // Cada producto se valida con las mismas reglas que "Agregar producto"
    foreach ($prods as $n => $p) {
        $error = validarDatosProducto([
            'nomProducto' => $p['nom'],
            'precio'      => $p['precio'],
            'talla'       => $p['talla'],
            'seccion'     => $p['seccion'],
        ]);
        if ($error !== '') {
            $error = 'Producto ' . ($n + 1) . ': ' . $error;
            break;
        }
    }

    if ($error === '') {
        header('Location: /galezarzal/local/crearLocal.php?paso=3');
        exit;
    }
}

// ── Paso 3 → validar y guardar horarios ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $paso === 3) {
    $dias     = diasValidos((array)($_POST['dias'] ?? []));
    $apertura = trim($_POST['apertura'] ?? '08:00');
    $cierre   = trim($_POST['cierre']   ?? '18:00');
    $descanso = trim($_POST['descanso'] ?? '');
    $_SESSION['local_wiz_hor'] = compact('dias','apertura','cierre','descanso');

    $error = validarHorario($apertura, $cierre, $descanso);
    if ($error === '') {
        header('Location: /galezarzal/local/crearLocal.php?paso=4');
        exit;
    }
}

// ── Paso 4 → revalidar todo, confirmar y guardar en BD ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $paso === 4) {
    $wiz  = $_SESSION['local_wiz']      ?? [];
    $prods= $_SESSION['local_wiz_prod'] ?? [];
    $hor  = $_SESSION['local_wiz_hor']  ?? [];

    if (empty($wiz)) { header('Location: /galezarzal/local/crearLocal.php?paso=1'); exit; }

    // Los datos de la sesión no son de confianza: se validan otra vez antes de guardar
    $error = validarDatosLocal([
        'nomLocal'     => $wiz['nomLocal']     ?? '',
        'describLocal' => $wiz['describLocal'] ?? '',
        'ubiLocal'     => $wiz['ubiLocal']     ?? '',
        'urlWhatsapp'  => $wiz['urlWA']        ?? '',
    ]) ?: validarCategoria((int)($wiz['idcategoria'] ?? 0), $categorias);

    foreach ($prods as $n => $p) {
        if ($error !== '') break;
        $error = validarDatosProducto([
            'nomProducto' => $p['nom']     ?? '',
            'precio'      => $p['precio']  ?? '',
            'talla'       => $p['talla']   ?? '',
            'seccion'     => $p['seccion'] ?? '',
        ]);
        if ($error !== '') $error = 'Producto ' . ($n + 1) . ': ' . $error;
    }

    if ($error === '' && !empty($hor['dias'])) {
        $error = validarHorario($hor['apertura'] ?? '', $hor['cierre'] ?? '', $hor['descanso'] ?? '');
    }

    if ($error === '') {
        // Crear/obtener vendedor
        if (!$vendedor) {
            $db->prepare("INSERT INTO Vendedor (iduser,nomVendedor) VALUES (?,?)")
               ->execute([$_SESSION['iduser'], $_SESSION['nombre']??'Vendedor']);
            $idVend = $db->lastInsertId();
            // Actualizar rol a Vendedor
            $stRol = $db->prepare("SELECT idRol FROM Rol WHERE nomRol='Vendedor'");
            $stRol->execute();
            $idRol = $stRol->fetchColumn();
            $db->prepare("UPDATE Usuario SET idRol=? WHERE iduser=?")->execute([$idRol,$_SESSION['iduser']]);
            $_SESSION['nomRol'] = 'Vendedor';
        } else {
            $idVend = $vendedor['idVendedor'];
        }

        // Horario
        $idHorario = null;
        if (!empty($hor['dias'])) {
            $db->prepare("INSERT INTO Horario (diasDisponibles,horarioApertura,horarioCierre,descansoOpc) VALUES (?,?,?,?)")
               ->execute([$hor['dias'],$hor['apertura'],$hor['cierre'],$hor['descanso']]);
            $idHorario = $db->lastInsertId();
        }

        // Local (el WhatsApp se guarda como 57 + 10 dígitos, listo para wa.me)
        $db->prepare("INSERT INTO Local (nomLocal,describLocal,ubiLocal,urlWhatsapp,idHorario,idVendedor,estado) VALUES (?,?,?,?,?,?,'pendiente')")
           ->execute([$wiz['nomLocal'],$wiz['describLocal'],$wiz['ubiLocal'],formatoWhatsapp($wiz['urlWA']),$idHorario,$idVend]);
        $idLocal = $db->lastInsertId();

        // Productos (el precio se convierte a número)
        foreach ($prods as $p) {
            $db->prepare("INSERT INTO Producto (nomProducto,precio,talla,seccion,idcategoria,idLocal) VALUES (?,?,?,?,?,?)")
               ->execute([$p['nom'],parsearPrecio($p['precio']),$p['talla'],$p['seccion'],$wiz['idcategoria'],$idLocal]);
        }

        unset($_SESSION['local_wiz'], $_SESSION['local_wiz_prod'], $_SESSION['local_wiz_hor']);
        header('Location: /galezarzal/local/miLocal.php?exito=1');
        exit;
    }
    // Si hubo error, se queda en el paso 4 mostrándolo
}

// ── Datos para vistas ──
$wiz   = $_SESSION['local_wiz']      ?? [];
$prods = $_SESSION['local_wiz_prod'] ?? [];
$hor   = $_SESSION['local_wiz_hor']  ?? [];
$diasSemana = DIAS_SEMANA;

$paginaActiva = 'locales'; $tituloPagina = 'Registrar local';
require_once __DIR__ . '/../includes/header.php';

// Helper steps
$completados = [$paso > 1, $paso > 2, $paso > 3, false];
?>

<section class="seccion" style="max-width:760px;">
  <div style="background:var(--oscuro);color:var(--blanco);border-radius:var(--radio);padding:1.5rem 2rem;margin-bottom:2rem;">
    <div class="flex-between">
      <div>
        <h1 style="font-size:1.4rem;">Registrar mi local en Galezarzal</h1>
        <p style="color:#aaa;font-size:.85rem;margin-top:.3rem;">En minutos tu local estará visible para los compradores.</p>
      </div>
      <a href="/galezarzal/index.php" class="btn-outline btn-sm">Volver al inicio</a>
    </div>
  </div>

  <!-- Steps -->
  <div class="steps" style="margin-bottom:2rem;">
    <?php
    $stepLabels = ['Información','Productos','Horarios','Confirmar'];
    for ($i=1;$i<=4;$i++): ?>
    <?php if ($i>1): ?><div class="step-line <?= $completados[$i-2] ? 'completada' : '' ?>"></div><?php endif; ?>
    <div class="step <?= $completados[$i-1] ? 'completado' : ($i===$paso ? 'activo' : '') ?>">
      <div class="step-num"><?= $completados[$i-1] ? '✓' : $i ?></div>
      <span class="step-label"><?= $stepLabels[$i-1] ?></span>
    </div>
    <?php endfor; ?>
  </div>

  <?php if ($error): ?><div class="alerta alerta-error" style="margin-bottom:1rem;"><?= htmlspecialchars($error) ?></div><?php endif; ?>

  <div style="background:var(--blanco);border-radius:var(--radio);box-shadow:var(--sombra);padding:2rem;">

  <?php if ($paso === 1): ?>
  <!-- PASO 1 -->
  <h2 style="font-size:1.1rem;font-weight:700;margin-bottom:1.25rem;">Paso 1: Información del local</h2>
  <form method="POST">
    <input type="hidden" name="paso" value="1">
    <div class="form-group"><label>Nombre del local *</label><input type="text" name="nomLocal" placeholder="Ej: Boutique Valentina" minlength="3" maxlength="100" value="<?= htmlspecialchars($wiz['nomLocal']??'') ?>" required></div>
    <div class="form-group"><label>Categoría principal *</label>
      <select name="idcategoria" required>
        <option value="">Selecciona...</option>
        <?php foreach ($categorias as $c): ?>
        <option value="<?= $c['idcategoria'] ?>" <?= ($wiz['idcategoria']??'')==$c['idcategoria']?'selected':'' ?>><?= htmlspecialchars($c['nomCategoria']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group"><label>Descripción</label><textarea name="describLocal" maxlength="300" placeholder="Describe tu local... Max 300 caracteres."><?= htmlspecialchars($wiz['describLocal']??'') ?></textarea></div>
    <div class="form-group"><label>Número de local en la galería</label><input type="text" name="ubiLocal" placeholder="Ej: Local 12, Pasillo B" maxlength="150" value="<?= htmlspecialchars($wiz['ubiLocal']??'') ?>"></div>
    <div class="form-group"><label>Teléfono / WhatsApp *</label><input type="tel" name="urlWhatsapp" placeholder="+57 300 123 4567" maxlength="20"
         pattern="(\+?57[\s\-]?)?3\d{2}[\s\-]?\d{3}[\s\-]?\d{4}"
         title="Celular colombiano de 10 dígitos que empiece por 3. Ej: 300 123 4567"
         value="<?= htmlspecialchars($wiz['urlWA']??'') ?>" required></div>
    <div class="form-acciones">
      <a href="/galezarzal/index.php" class="btn-outline">Cancelar</a>
      <button type="submit" class="btn-primary">Siguiente →</button>
    </div>
  </form>

  <?php elseif ($paso === 2): ?>
  <!-- PASO 2 -->
  <h2 style="font-size:1.1rem;font-weight:700;margin-bottom:.5rem;">Paso 2: Agrega tus primeros productos</h2>
  <p style="font-size:.85rem;color:var(--gris);margin-bottom:1.25rem;">Puedes agregar más productos después desde Configuraciones.</p>
  <form method="POST" id="formProds">
    <input type="hidden" name="paso" value="2">
    <div id="prods-container">
      <?php $numProds = max(2, count($prods)); for($pi=0;$pi<$numProds;$pi++): $pd=$prods[$pi]??[]; ?>
      <div class="prod-item" style="border:1px solid #eee;border-radius:var(--radio);padding:1rem;margin-bottom:1rem;">
        <strong style="font-size:.9rem;">Producto <?= $pi+1 ?></strong>
        <div class="form-row" style="margin-top:.5rem;">
          <div class="form-group"><label>Nombre</label><input type="text" name="nomProducto[]" placeholder="Ej: Vestido floral" minlength="2" maxlength="80" value="<?= htmlspecialchars($pd['nom']??'') ?>"></div>
          <div class="form-group"><label>Precio</label><input type="text" name="precio[]" placeholder="Ej: 45000" inputmode="numeric" maxlength="14"
               pattern="\d{1,3}(\.\d{3})+|\d+([.,]\d{1,2})?" title="Solo números mayores que 0. Ej: 45000 o 45.000"
               value="<?= htmlspecialchars($pd['precio']??'') ?>"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Tallas</label><input type="text" name="talla[]" placeholder="S, M, L" maxlength="30" value="<?= htmlspecialchars($pd['talla']??'') ?>"></div>
          <div class="form-group"><label>Sección</label>
            <select name="seccion[]">
              <option value="">Ninguna</option>
              <?php foreach (SECCIONES_PRODUCTO as $sec): ?>
              <option <?= ($pd['seccion']??'')===$sec?'selected':'' ?>><?= $sec ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </div>
      <?php endfor; ?>
    </div>
    <button type="button" onclick="agregarProducto()" class="btn-outline btn-sm mb-2">+ Agregar otro producto</button>
    <div class="form-acciones">
      <a href="/galezarzal/local/crearLocal.php?paso=1" class="btn-outline">← Atrás</a>
      <button type="submit" class="btn-primary">Siguiente →</button>
    </div>
  </form>
  <script>
  let cont = <?= $numProds ?>;
  // Copia el primer bloque (con todas sus validaciones) y lo deja vacío
  function agregarProducto() {
    cont++;
    const div = document.querySelector('.prod-item').cloneNode(true);
    div.querySelectorAll('input').forEach(i => i.value = '');
    div.querySelector('select').selectedIndex = 0;
    div.querySelector('strong').textContent = 'Producto ' + cont;
    document.getElementById('prods-container').appendChild(div);
  }
  </script>

  <?php elseif ($paso === 3): ?>
  <!-- PASO 3 -->
  <h2 style="font-size:1.1rem;font-weight:700;margin-bottom:1.25rem;">Paso 3: Define tus horarios de atención</h2>
  <form method="POST">
    <input type="hidden" name="paso" value="3">
    <p style="font-weight:600;color:var(--rojo);font-size:.88rem;margin-bottom:.5rem;">Días disponibles:</p>
    <div class="dias-grid">
      <?php $diasActivos = array_flip(explode(',', $hor['dias']??'LUN,MAR,MIE,JUE,VIE,SAB'));
      foreach ($diasSemana as $d): ?>
      <label class="dia-btn <?= isset($diasActivos[$d]) ? 'activo' : '' ?>" style="cursor:pointer;user-select:none;">
        <input type="checkbox" name="dias[<?= $d ?>]" value="1" style="display:none;" <?= isset($diasActivos[$d])?'checked':'' ?>
               onchange="this.closest('.dia-btn').classList.toggle('activo',this.checked)">
        <?= $d ?>
      </label>
      <?php endforeach; ?>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Hora de apertura</label><input type="time" name="apertura" required value="<?= htmlspecialchars($hor['apertura']??'08:00') ?>"></div>
      <div class="form-group"><label>Hora de cierre</label><input type="time" name="cierre" required value="<?= htmlspecialchars($hor['cierre']??'18:00') ?>"></div>
    </div>
    <div class="form-group"><label>Descanso (opcional)</label><input type="text" name="descanso" placeholder="Ej: 12:30 pm - 2:00 pm" maxlength="50" value="<?= htmlspecialchars($hor['descanso']??'') ?>"></div>
    <div class="form-acciones">
      <a href="/galezarzal/local/crearLocal.php?paso=2" class="btn-outline">← Atrás</a>
      <button type="submit" class="btn-primary">Siguiente →</button>
    </div>
  </form>

  <?php elseif ($paso === 4): ?>
  <!-- PASO 4 -->
  <h2 style="font-size:1.1rem;font-weight:700;margin-bottom:1.25rem;">Paso 4: Revisa y confirma tu local</h2>
  <div style="background:var(--crema);border-radius:var(--radio);padding:1.25rem;margin-bottom:1.5rem;">
    <p><strong>Nombre del local:</strong> <?= htmlspecialchars($wiz['nomLocal']??'—') ?></p>
    <p style="margin-top:.4rem;"><strong>Número de local:</strong> <?= htmlspecialchars($wiz['ubiLocal']??'—') ?></p>
    <p style="margin-top:.4rem;"><strong>Horarios:</strong> <?= htmlspecialchars($hor['dias']??'—') ?> <?= htmlspecialchars($hor['apertura']??'') ?> - <?= htmlspecialchars($hor['cierre']??'') ?></p>
    <p style="margin-top:.4rem;"><strong>Productos ingresados:</strong> <?= count($prods) ?> productos</p>
    <p style="margin-top:.75rem;background:#fef9e7;border-left:3px solid var(--alerta);padding:.6rem .9rem;border-radius:4px;font-size:.88rem;">
      Estado inicial: <strong>En revisión por el admin</strong>.<br>
      Tu local quedará en revisión hasta que el administrador lo apruebe. Te notificaremos por correo.
    </p>
  </div>
  <form method="POST">
    <input type="hidden" name="paso" value="4">
    <div class="form-acciones">
      <a href="/galezarzal/local/crearLocal.php?paso=3" class="btn-outline">← Atrás</a>
      <button type="submit" class="btn-primary">PUBLICAR MI LOCAL</button>
    </div>
  </form>
  <?php endif; ?>

  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>