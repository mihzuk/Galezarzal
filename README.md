# Galezarzal

Galezarzal es un marketplace web de moda local para la Galería de Zarzal, Valle del Cauca. Permite explorar locales y catálogos por categoría, administrar productos y favoritos, y contactar a los vendedores por WhatsApp. Está desarrollado con PHP y MySQL/MariaDB y no incluye pasarela de pago.

## Requisitos

- XAMPP para Windows con PHP 8.0 o superior. Se recomienda la serie 8.2, que incluye PHP 8.2 y MariaDB 10.4. El código utiliza `match` y `str_contains`, funciones que no existen en PHP 7, por lo que una versión anterior de XAMPP no funcionará.
- MariaDB 10.4 o superior. El script de la base de datos fue exportado desde la versión 10.4.32.
- Git, solo si se desea clonar el repositorio.

## Instalación en local con XAMPP

1. Copie el proyecto en `C:\xampp\htdocs\galezarzal`. La carpeta debe llamarse exactamente así, en minúsculas, porque los enlaces internos de la aplicación apuntan a `/galezarzal/`.

   Si usa Git, ejecute:

   ```
   git clone https://github.com/mihzuk/Galezarzal.git C:\xampp\htdocs\galezarzal
   ```

   Si descarga el ZIP desde GitHub, descomprímalo y cambie el nombre de la carpeta `Galezarzal-main` por `galezarzal`. Verifique que el archivo `index.php` quede directamente dentro de `galezarzal` y no en una subcarpeta adicional.

2. Abra el Panel de Control de XAMPP e inicie los módulos Apache y MySQL.

3. Cree la base de datos desde phpMyAdmin (http://localhost/phpmyadmin):

   1. Haga clic en Nueva, escriba `galezarzal` como nombre, elija el cotejamiento `utf8mb4_unicode_ci` y presione Crear.
   2. Seleccione la base `galezarzal` en el panel izquierdo. Este paso es necesario porque el script no incluye `CREATE DATABASE` ni `USE`; si se importa sin seleccionarla, aparece el error "No database selected".
   3. Abra la pestaña Importar, elija el archivo `database/galezarzalBD.sql` y presione Importar.
   4. Al terminar, la base debe contener 13 tablas.

   Si necesita repetir la importación, elimine primero la base desde Operaciones y créela de nuevo. El script no usa `IF NOT EXISTS`, así que sobre tablas ya creadas falla con el error "Table already exists".

4. Abra http://localhost/galezarzal/ en el navegador. Debe aparecer la página de inicio con el local de ejemplo "el niche store".

La conexión a la base de datos se configura en `includes/config.php` (usuario `root`, sin contraseña, base `galezarzal`). Esta configuración es solo para desarrollo y no debe usarse en un servidor público.

## Primeros pasos en la aplicación

Los datos incluidos en `database/galezarzalBD.sql` son ficticios. El usuario de ejemplo (`demo@example.com`, con rol de administrador) no tiene una contraseña válida, por lo que no es posible iniciar sesión con él. Para probar la aplicación es necesario crear una cuenta propia.

Para registrarse, ingrese a http://localhost/galezarzal/usuario/ingresarCuenta.php, abra la pestaña Crear cuenta y complete el formulario; la contraseña debe tener al menos seis caracteres. La cuenta se crea con el rol de comprador y luego se puede iniciar sesión.

Para registrar un local, inicie sesión y use el botón "tengo un local" de la página de inicio, que abre un asistente de cuatro pasos. Al finalizar, el usuario pasa a tener rol de vendedor y el local queda en estado pendiente. Un local pendiente no aparece en el inicio ni en el buscador hasta que un administrador lo apruebe.

La aplicación no incluye ningún administrador utilizable, así que es necesario asignar el rol manualmente. En phpMyAdmin, con la base `galezarzal` seleccionada, abra la pestaña SQL y ejecute lo siguiente, reemplazando el correo por el de su cuenta:

```sql
UPDATE usuario SET idRol = 3 WHERE email = 'tu_correo@ejemplo.com';
```

Después cierre sesión y vuelva a iniciarla, porque el rol se guarda en la sesión al ingresar. En el menú de Configuración aparecerá la sección de administración, desde donde se pueden gestionar usuarios, locales y productos. Los locales pendientes se aprueban en http://localhost/galezarzal/admin/moderarLocales.php.

## Solución de problemas

Si aparece un error 404, la página se ve sin estilos o los enlaces no funcionan, lo más probable es que la carpeta no se llame `galezarzal` o que haya quedado anidada dentro de otra. Revise el paso 1 de la instalación.

Si aparece el mensaje "Error de conexión a la base de datos", verifique que MySQL esté iniciado, que la base se llame `galezarzal` y que el usuario `root` no tenga contraseña. Si la tiene, ajuste `DB_PASS` en `includes/config.php`.

Si aparecen errores de sintaxis o el mensaje `Call to undefined function str_contains()`, la versión de PHP es anterior a la 8.0 y es necesario actualizar XAMPP.

Si Apache no inicia porque el puerto 80 está ocupado, cambie el puerto en la configuración de XAMPP y acceda mediante `http://localhost:PUERTO/galezarzal/`.

## Limitaciones conocidas en Linux y macOS

Esta guía está pensada para XAMPP en Windows. En sistemas que distinguen entre mayúsculas y minúsculas existen dos problemas pendientes en el código:

- La carpeta se llama `Local/`, pero los enlaces apuntan a `/galezarzal/local/...`, por lo que esas rutas devuelven un error 404.
- Las consultas usan nombres de tabla con mayúscula inicial (`Usuario`, `Rol`, `Local`), mientras que el script crea las tablas en minúscula. En estos sistemas MySQL y MariaDB no las encuentran, a menos que se configure `lower_case_table_names=1` en `my.cnf`. En Windows ese valor ya viene activado por defecto.

## Flujo de trabajo

La rama `main` está protegida: todos los cambios deben entrar mediante un pull request con aprobación. Cada tarea se desarrolla en una rama propia (`feature/...`, `fix/...` o `docs/...`) y, al terminar, se abre un pull request hacia `main`.

