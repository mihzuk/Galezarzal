# Galezarzal

Galezarzal es un marketplace web que conecta a los compradores con los emprendedores textiles de la Galería de Zarzal, Valle del Cauca. Cada local de la Galería puede publicar su catálogo en línea, y cada persona puede recorrerlo desde su celular, comparar estilos, tallas y precios, y escribirle directamente al vendedor por WhatsApp, sin intermediarios.

El proyecto parte de una idea sencilla: los locales de la Galería tienen mucho que mostrar y, hasta ahora, poca presencia digital. Galezarzal les da un espacio propio donde publicar sus productos, y a los clientes, un lugar único donde encontrarlos.

## Contenido

- [Qué se puede hacer en la plataforma](#qué-se-puede-hacer-en-la-plataforma)
- [Tecnologías](#tecnologías)
- [Estructura del repositorio](#estructura-del-repositorio)
- [Instalación en local con XAMPP](#instalación-en-local-con-xampp)
  - [Requisitos](#requisitos)
  - [Pasos](#pasos)
- [Primeros pasos en la aplicación](#primeros-pasos-en-la-aplicación)
- [Solución de problemas](#solución-de-problemas)
- [Alcance y limitaciones](#alcance-y-limitaciones)
- [Equipo](#equipo)
- [Cómo contribuir](#cómo-contribuir)

## Qué se puede hacer en la plataforma

La aplicación tiene tres tipos de usuario, cada uno con su propio recorrido.

**Compradores.** Pueden explorar los locales y filtrarlos por categoría, entrar al perfil de cada local para ver su catálogo, guardar locales en favoritos y contactar al vendedor por WhatsApp con un solo clic. También reciben notificaciones, pueden consultar las preguntas frecuentes y enviar un mensaje desde la sección de ayuda.

**Vendedores.** Registran su local mediante un asistente de cuatro pasos (datos del local, productos, horarios y confirmación) y luego lo administran desde su panel: información del local, catálogo de productos con precio y talla, horarios de atención y visibilidad. Un local nuevo queda pendiente de revisión y se hace público cuando un administrador lo aprueba.

**Administradores.** Gestionan usuarios, moderan locales y productos, y ajustan la configuración general de la plataforma: nombre, dirección del sitio, modo mantenimiento y apertura del registro.

Todos los usuarios pueden crear y modificar su cuenta, cambiar la contraseña, configurar sus notificaciones y su privacidad, o eliminar la cuenta.

Los locales se organizan en seis categorías: Ropa Mujer, Ropa Hombre, Ropa Niños, Accesorios, Calzado y Ropa Deportiva.

## Tecnologías

- PHP 8 con acceso a datos mediante PDO.
- MySQL / MariaDB para la base de datos.
- HTML y CSS propios, con una paleta definida en `assets/css/style.css`: rojo `#E94560`, azul oscuro `#1A1A2E` y crema `#F1FAEE`.
- XAMPP como entorno de desarrollo local.

## Estructura del repositorio

```
galezarzal/
├── index.php            Página de inicio
├── Local/               Búsqueda, perfil y creación de locales
├── catalogo/            Catálogo y productos de cada local
├── favoritos/           Locales guardados por el comprador
├── notificaciones/      Avisos para el usuario
├── usuario/             Registro, inicio de sesión y cuenta
├── configuracion/       Panel del vendedor y ajustes personales
├── admin/               Moderación y configuración de la plataforma
├── ayudas/              Preguntas frecuentes y contacto
├── includes/            Conexión, sesión, encabezado y pie de página
├── assets/css/          Estilos
└── database/            Script SQL con la estructura y datos de ejemplo
```

La base de datos tiene 13 tablas. Las principales son `usuario`, `rol`, `vendedor`, `local`, `producto`, `categoria`, `horario`, `favorito` y `notificacion`; el resto (`ciudad`, `departamento`, `tipodocumento` y `origenusuario`) apoya el registro de usuarios.

## Instalación en local con XAMPP

### Requisitos

- XAMPP para Windows con PHP 8.0 o superior. Se recomienda la serie 8.2, que incluye PHP 8.2 y MariaDB 10.4. El código usa `match` y `str_contains`, que no existen en PHP 7, de modo que una versión anterior de XAMPP no funcionará.
- MariaDB 10.4 o superior.
- Git, solo si se desea clonar el repositorio.

### Pasos

1. Copie el proyecto en `C:\xampp\htdocs\galezarzal`. La carpeta debe llamarse exactamente así, en minúsculas, porque los enlaces internos de la aplicación apuntan a `/galezarzal/`.

   Con Git:

   ```
   git clone https://github.com/mihzuk/Galezarzal.git C:\xampp\htdocs\galezarzal
   ```

   Si descarga el ZIP desde GitHub, descomprímalo y cambie el nombre de la carpeta `Galezarzal-main` por `galezarzal`. Verifique que `index.php` quede directamente dentro de `galezarzal` y no en una subcarpeta adicional.

2. Abra el Panel de Control de XAMPP e inicie los módulos Apache y MySQL.

3. Cree la base de datos desde phpMyAdmin (http://localhost/phpmyadmin):

   1. Haga clic en Nueva, escriba `galezarzal` como nombre, elija el cotejamiento `utf8mb4_unicode_ci` y presione Crear.
   2. Seleccione la base `galezarzal` en el panel izquierdo. El script no incluye `CREATE DATABASE` ni `USE`; si se importa sin seleccionarla, aparece el error "No database selected".
   3. Abra la pestaña Importar, elija el archivo `database/galezarzalBD.sql` y presione Importar.
   4. Al terminar, la base debe contener 13 tablas.

   Para repetir la importación, elimine primero la base desde Operaciones y créela de nuevo; el script no usa `IF NOT EXISTS` y, sobre tablas ya creadas, falla con "Table already exists".

4. Abra http://localhost/galezarzal/ en el navegador. Debe aparecer la página de inicio con el local de ejemplo "el niche store".

La conexión a la base de datos se configura en `includes/config.php` (usuario `root`, sin contraseña, base `galezarzal`). Es una configuración pensada solo para desarrollo y no debe usarse en un servidor público.

## Primeros pasos en la aplicación

Los datos de `database/galezarzalBD.sql` son ficticios. El usuario de ejemplo (`demo@example.com`, con rol de administrador) no tiene una contraseña válida y no permite iniciar sesión, así que para probar la aplicación hay que crear una cuenta propia.

Para registrarse, ingrese a http://localhost/galezarzal/usuario/ingresarCuenta.php, abra la pestaña Crear cuenta y complete el formulario; la contraseña debe tener al menos seis caracteres. La cuenta nace con rol de comprador.

Para publicar un local, inicie sesión y use el botón "tengo un local" de la página de inicio. Al terminar el asistente, el usuario pasa a ser vendedor y su local queda pendiente: no aparecerá en el inicio ni en el buscador hasta que un administrador lo apruebe.

La aplicación no incluye ningún administrador utilizable, por lo que el rol se asigna manualmente. En phpMyAdmin, con la base `galezarzal` seleccionada, abra la pestaña SQL y ejecute lo siguiente, cambiando el correo por el de su cuenta:

```sql
UPDATE usuario SET idRol = 3 WHERE email = 'tu_correo@ejemplo.com';
```

Después cierre sesión y vuelva a iniciarla, porque el rol se guarda en la sesión al ingresar. En el menú de Configuración aparecerá la sección de administración. Los locales pendientes se aprueban en http://localhost/galezarzal/admin/moderarLocales.php.

## Solución de problemas

Si aparece un error 404, la página se ve sin estilos o los enlaces no funcionan, lo más probable es que la carpeta no se llame `galezarzal` o que haya quedado dentro de otra. Revise el primer paso de la instalación.

Si aparece "Error de conexión a la base de datos", verifique que MySQL esté iniciado, que la base se llame `galezarzal` y que el usuario `root` no tenga contraseña. Si la tiene, ajuste `DB_PASS` en `includes/config.php`.

Si aparecen errores de sintaxis o el mensaje `Call to undefined function str_contains()`, la versión de PHP es anterior a la 8.0 y es necesario actualizar XAMPP.

Si Apache no inicia porque el puerto 80 está ocupado, cambie el puerto en la configuración de XAMPP y acceda mediante `http://localhost:PUERTO/galezarzal/`.

## Alcance y limitaciones

Galezarzal es una vitrina y un canal de contacto, no una tienda con pago en línea: la compra se acuerda directamente entre comprador y vendedor por WhatsApp, y el proyecto no incluye pasarela de pago.

La recuperación de contraseña es una simulación. El formulario existe, pero el envío del correo con el enlace de restablecimiento no está implementado.

La guía de instalación está pensada para XAMPP en Windows. En Linux y macOS, que distinguen entre mayúsculas y minúsculas, hay dos problemas pendientes en el código: la carpeta se llama `Local/` mientras que los enlaces apuntan a `/galezarzal/local/...`, lo que produce errores 404; y las consultas usan nombres de tabla con mayúscula inicial (`Usuario`, `Rol`, `Local`) que no coinciden con las tablas en minúscula del script, a menos que se configure `lower_case_table_names=1` en `my.cnf`. En Windows ese valor ya viene activado por defecto.

## Equipo

Responsables del repositorio: [@josdaver3000](https://github.com/josdaver3000), [@Johan-v-p](https://github.com/Johan-v-p), [@mihzuk](https://github.com/mihzuk) y [@lizarazo2005](https://github.com/lizarazo2005).

## Cómo contribuir

La rama `main` está protegida: nadie sube cambios directamente, todo entra mediante un pull request con aprobación. El trabajo se organiza así: una tarea equivale a un Issue, una rama y un pull request.

1. Elegir un Issue del Backlog en el Project, asignárselo y pasarlo a In progress.
2. Actualizar `main` y crear una rama nueva para la tarea:

   ```
   git switch main
   git pull origin main
   git switch -c feature/nombre-de-la-tarea
   ```

   Se usa `feature/...` para funciones nuevas, `fix/...` para correcciones y `docs/...` para documentación.

3. Hacer los cambios, probarlos en XAMPP y subir la rama:

   ```
   git add .
   git commit -m "feat: descripción breve del cambio"
   git push -u origin feature/nombre-de-la-tarea
   ```

4. Abrir un pull request hacia `main` y escribir en la descripción `Closes #número-del-issue`.
5. Esperar la revisión. Si se piden cambios, se corrigen en la misma rama y se hace push; el pull request se actualiza solo.
6. Una vez aprobado y unido a `main`, borrar la rama. Para la siguiente tarea se crea una rama nueva, sin reutilizar la anterior, y siempre después de hacer `git pull` en `main`.
