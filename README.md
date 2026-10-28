# Galezarzal

Marketplace web de moda local para la Galería de Zarzal, Valle del Cauca. Permite explorar locales y catálogos por categoría, gestionar productos y favoritos, y contactar a los vendedores por WhatsApp. Desarrollado con PHP y MySQL/MariaDB. No incluye pasarela de pago.

## Ejecutar en local (XAMPP)
1. Clonar el repo en `C:\xampp\htdocs\galezarzal`.
2. Iniciar Apache y MySQL desde XAMPP.
3. En phpMyAdmin crear la base `galezarzal` (utf8mb4) e importar `database/galezarzalBD.sql`.
4. Abrir http://localhost/galezarzal/

La conexión local está en `includes/config.php` (usuario `root`, sin contraseña). No usar esa configuración en un servidor público.

> Los datos de `database/galezarzalBD.sql` son ficticios; el usuario de ejemplo no tiene contraseña válida, regístrate desde la app.

## Flujo de trabajo
- `main` está protegida: todo cambio entra por pull request con aprobación.
- Una rama por tarea (`feature/...`, `fix/...`, `docs/...`), luego pull request hacia `main`.
