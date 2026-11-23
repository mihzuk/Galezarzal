<?php
// Redirige al perfil o al login según estado de sesión
require_once __DIR__ . '/../includes/aut_verifica.php';
if (sesionActiva()) {
    header('Location: ' . BASE_PATH . '/configuracion/configuracion.php');
} else {
    header('Location: ' . BASE_PATH . '/usuario/ingresarCuenta.php');
}
exit;