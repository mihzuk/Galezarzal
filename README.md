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
## Diagrama de la Base de Datos

```mermaid
erDiagram
    CATEGORIA ||--o{ PRODUCTO : contiene
    DEPARTAMENTO ||--o{ CIUDAD : tiene
    CIUDAD ||--o{ ORIGENUSUARIO : pertenece
    DEPARTAMENTO ||--o{ ORIGENUSUARIO : pertenece
    USUARIO ||--o{ FAVORITO : guarda
    LOCAL ||--o{ FAVORITO : guardado_en
    PRODUCTO ||--o{ FAVORITO : guardado_en
    HORARIO ||--o{ LOCAL : asignado_a
    VENDEDOR ||--o{ LOCAL : administra
    USUARIO ||--o{ NOTIFICACION : recibe
## Diagrama de la Base de Datos

![Diagrama ER de la Base de Datos](docs/diagrama_bd.png)
    LOCAL ||--o{ PRODUCTO : vende
    ROL ||--o{ USUARIO : define
    TIPODOCUMENTO ||--o{ USUARIO : identifica
    ORIGENUSUARIO ||--o| USUARIO : origen_de
    USUARIO ||--o| VENDEDOR : es
