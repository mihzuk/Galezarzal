<?php
// Redirige al perfil o al login según estado de sesión
require_once __DIR__ . '/../includes/aut_verifica.php';
if (sesionActiva()) {
    header('Location: /galezarzal/configuracion/configuracion.php');
} else {
    header('Location: /galezarzal/usuario/ingresarCuenta.php');
}
exit;