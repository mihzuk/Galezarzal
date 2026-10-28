<?php
// ============================================================
//  GALEZARZAL — FAQ / Ayuda y Contacto
// ============================================================
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/aut_verifica.php';

$enviado = false;
$errMsg  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre  = trim($_POST['nombre']  ?? '');
    $email   = trim($_POST['email']   ?? '');
    $asunto  = trim($_POST['asunto']  ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');

    if (!$nombre || !$email || !$mensaje) {
        $errMsg = 'Por favor completa todos los campos obligatorios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errMsg = 'Ingresa un correo válido.';
    } else {
        // En producción: mail() o API de correo
        $enviado = true;
    }
}

$faqs = [
    ['¿Cómo publico mi local?',
     'Crea una cuenta, inicia sesión y haz clic en "Tengo un local". Completa el formulario con el nombre de tu local, ubicación y WhatsApp. Tu local quedará en revisión y se publicará en 24–48 horas.'],
    ['¿Cómo contacto a un vendedor?',
     'Cada local tiene un botón de WhatsApp. Al hacer clic serás redirigido directamente al chat del vendedor con un mensaje predeterminado sobre el producto de tu interés.'],
    ['¿Es gratis usar Galezarzal?',
     'Sí. Tanto para compradores como para vendedores de la Galería de Zarzal el uso es completamente gratuito durante el período de lanzamiento.'],
    ['¿Cómo agrego productos a mi catálogo?',
     'Desde "Mi Local" haz clic en "+ Agregar producto". Puedes subir fotos mediante URL (usa servicios como Imgur o Google Photos), definir precio, tallas, colores y categoría.'],
    ['¿Qué hago si olvidé mi contraseña?',
     'En la pantalla de inicio de sesión haz clic en "¿Olvidaste tu contraseña?" e ingresa tu correo. Recibirás instrucciones para restablecerla.'],
    ['¿Puedo vender cosas que no sean de la Galería de Zarzal?',
     'No. Galezarzal es una plataforma exclusiva para los emprendedores y locales ubicados físicamente en la Galería Comercial de Zarzal, Valle del Cauca.'],
    ['¿Cómo elimino un producto?',
     'Desde "Mi Local" haz clic en "Editar" al lado del producto y en la parte inferior encontrarás la opción "Eliminar producto".'],
    ['¿Cuántos locales puedo registrar?',
     'Por ahora cada vendedor puede registrar un solo local. Si tienes más de uno, contáctanos a través de este formulario.'],
];

$paginaActiva = 'contactos';
$tituloPagina = 'Ayuda y Contacto';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="hero" style="padding:2.5rem 2rem;">
  <div class="hero-inner">
    <p class="hero-sub">Centro de ayuda</p>
    <h1 style="font-size:2rem;">¿En qué podemos <span>ayudarte?</span></h1>
    <p>Encuentra respuestas rápidas o escríbenos directamente.</p>
  </div>
</section>

<section class="seccion" style="max-width:800px;">
  <h2 style="font-size:1.2rem;font-weight:700;margin-bottom:1.25rem;">Preguntas frecuentes</h2>

  <div style="display:flex;flex-direction:column;gap:.75rem;margin-bottom:3rem;">
    <?php foreach ($faqs as $i => [$preg, $resp]): ?>
    <details style="background:var(--blanco);border-radius:var(--radio);box-shadow:var(--sombra);overflow:hidden;">
      <summary style="padding:1rem 1.25rem;cursor:pointer;font-weight:600;font-size:.95rem;list-style:none;display:flex;justify-content:space-between;align-items:center;">
        <?= htmlspecialchars($preg) ?>
        <span style="font-size:1.1rem;color:var(--rojo);">+</span>
      </summary>
      <p style="padding:.75rem 1.25rem 1rem;color:#555;font-size:.9rem;line-height:1.6;border-top:1px solid #f0f0f0;">
        <?= htmlspecialchars($resp) ?>
      </p>
    </details>
    <?php endforeach; ?>
  </div>

  <!-- Formulario de contacto -->
  <h2 style="font-size:1.2rem;font-weight:700;margin-bottom:1rem;">¿No encontraste lo que buscabas?</h2>

  <?php if ($enviado): ?>
    <div class="alerta alerta-exito">
      ✅ Mensaje enviado correctamente. Te responderemos pronto a tu correo.
    </div>
  <?php else: ?>
    <?php if ($errMsg): ?>
      <div class="alerta alerta-error"><?= htmlspecialchars($errMsg) ?></div>
    <?php endif; ?>
    <div class="form-wrap" style="max-width:100%;">
      <form method="POST">
        <div class="form-row">
          <div class="form-group">
            <label>Tu nombre *</label>
            <input type="text" name="nombre" required
                   value="<?= htmlspecialchars($_POST['nombre'] ?? ($_SESSION['nombre'] ?? '')) ?>">
          </div>
          <div class="form-group">
            <label>Correo electrónico *</label>
            <input type="email" name="email" required
                   value="<?= htmlspecialchars($_POST['email'] ?? ($_SESSION['email'] ?? '')) ?>">
          </div>
        </div>
        <div class="form-group">
          <label>Asunto</label>
          <input type="text" name="asunto" placeholder="Problema con mi local, sugerencia, etc."
                 value="<?= htmlspecialchars($_POST['asunto'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Mensaje *</label>
          <textarea name="mensaje" rows="4" required
                    placeholder="Cuéntanos tu duda o problema con detalle…"><?= htmlspecialchars($_POST['mensaje'] ?? '') ?></textarea>
        </div>
        <button type="submit" class="btn-primary">Enviar mensaje</button>
      </form>
    </div>
  <?php endif; ?>
</section>

<style>
details summary::-webkit-details-marker { display:none; }
details[open] summary span { transform: rotate(45deg); display:inline-block; }
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
