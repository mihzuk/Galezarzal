<?php
// ============================================================
//  GALEZARZAL — Verificación de autenticación y roles
// ============================================================
require_once __DIR__ . '/config.php';

function sesionActiva(): bool {
    if (session_status() === PHP_SESSION_NONE) session_start();
    return isset($_SESSION['iduser']);
}

function requiereLogin(string $redirigir = '/galezarzal/usuario/ingresarCuenta.php'): void {
    if (!sesionActiva()) {
        header('Location: ' . $redirigir);
        exit;
    }
}

function requiereRol(array $roles): void {
    requiereLogin();
    if (!in_array($_SESSION['nomRol'] ?? '', $roles, true)) {
        header('Location: /galezarzal/index.php?error=acceso_denegado');
        exit;
    }
}

function esAdmin(): bool {
    return sesionActiva() && ($_SESSION['nomRol'] ?? '') === 'Administrador';
}

function esVendedor(): bool {
    return sesionActiva() && ($_SESSION['nomRol'] ?? '') === 'Vendedor';
}

function usuarioActual(): array {
    if (session_status() === PHP_SESSION_NONE) session_start();
    return $_SESSION ?? [];
}