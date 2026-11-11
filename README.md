# Galezarzal

Galezarzal es un marketplace web para los comerciantes de la Galería de Zarzal, Valle del Cauca. Cada local puede publicar su catálogo en línea, y cualquier persona puede entrar al sitio desde el navegador, ver los productos, comparar estilos, tallas y precios, y escribirle al vendedor por WhatsApp sin pasar por intermediarios.

## Qué se puede hacer

Hay tres tipos de usuario y cada uno tiene su propio recorrido.

**Compradores.** Pueden explorar los locales y filtrarlos por categoría, entrar al perfil de cada uno para ver su catálogo, guardar sus favoritos y escribirle al vendedor por WhatsApp con un clic. También reciben notificaciones, pueden leer las preguntas frecuentes y mandar un mensaje desde la sección de ayuda.

**Vendedores.** Registran su local con un asistente de cuatro pasos (datos del local, productos, horarios y confirmación) y después lo manejan desde su panel: información, catálogo con precio y talla, horarios de atención y visibilidad. Un local nuevo queda pendiente y solo se hace público cuando un administrador lo aprueba.

**Administradores.** Gestionan usuarios, moderan locales y productos, y ajustan la configuración general: nombre del sitio, dirección, modo mantenimiento y si el registro está abierto o cerrado.

Todos pueden crear y editar su cuenta, cambiar la contraseña, configurar notificaciones y privacidad, o borrar la cuenta. Los locales se clasifican en seis categorías: Ropa Mujer, Ropa Hombre, Ropa Niños, Accesorios, Calzado y Ropa Deportiva.

## Con qué está hecho

PHP 8 con PDO para hablar con la base de datos, MySQL o MariaDB para guardar los datos, y HTML y CSS escritos a mano. Los colores del sitio están en `assets/css/style.css`: rojo `#E94560`, azul oscuro `#1A1A2E` y crema `#F1FAEE`. Para desarrollar usamos XAMPP.

## Antes de instalar: una advertencia sobre PHP

El código usa `match` y `str_contains`, así que necesitas **PHP 8.0 o superior**. Pero no te quedes con cualquier XAMPP que encuentres, porque los instaladores viejos traen una vulnerabilidad seria.

En junio de 2024 se publicó el **CVE-2024-4577**, una falla crítica (9.8 sobre 10) en PHP para Windows. Permite que alguien de afuera ejecute código en el servidor aprovechando cómo PHP-CGI interpreta ciertos caracteres. Quedó corregida en PHP 8.1.29, 8.2.20 y 8.3.8, así que cualquier versión anterior a esas, o una rama que ya no recibe soporte, sigue expuesta.

Lo que lo hace delicado para nosotros es que XAMPP en Windows deja por defecto la carpeta de PHP accesible desde Apache mediante un `ScriptAlias`, y con eso basta para que sea explotable aunque no uses CGI a propósito. El caso documentado es con Windows configurado en chino o japonés, pero no conviene apostar a que el idioma te protege. Los ataques empezaron a los pocos días de publicarse la falla, incluso con ransomware.

Esto significa que los XAMPP 8.2.12, 8.1.25 y 8.0.30 de noviembre de 2023 (los que se recomendaban hasta hace poco) traen un PHP anterior al parche. Si ya los tienes instalados, actualiza.

Lo que recomendamos:

- Instalar la versión más reciente de XAMPP desde [apachefriends.org](https://www.apachefriends.org/download.html). Al momento de escribir esto es la 8.5.12 (octubre de 2026), que trae PHP 8.5.12 y MariaDB 10.11.16.
- Si no necesitas CGI, abrir `C:\xampp\apache\conf\extra\httpd-xampp.conf` y comentar con `#` la línea `ScriptAlias /php-cgi/ "C:/xampp/php/"`. Reinicia Apache después.
- No publicar este proyecto en internet tal como está. La configuración de la base de datos (usuario `root` sin contraseña) es solo para trabajar en local.

### ¿Se puede usar un MariaDB más nuevo con el PHP reciente?

Sí. PHP no está atado a una versión de MariaDB: el proyecto solo usa PDO con el driver de MySQL, y el script SQL funciona desde MariaDB 10.4 en adelante. XAMPP trae MariaDB 10.11, que sigue siendo una versión soportada, así que para trabajar no necesitas cambiar nada.

Si de todas formas quieres una versión más reciente (por ejemplo una 11.x), se puede cambiar el motor dentro de XAMPP a mano. No es un procedimiento oficial de XAMPP, así que haz el respaldo antes y guarda la carpeta vieja por si toca volver atrás:

1. Abre el Shell del panel de XAMPP y saca un respaldo de todo: `mysqldump -u root -p --all-databases > respaldo.sql`
2. Detén Apache y MySQL desde el panel y ciérralo.
3. Renombra `C:\xampp\mysql` a `mysql-old`.
4. Descarga el ZIP de Windows de la versión que quieras desde [mariadb.org/download](https://mariadb.org/download/) y descomprímelo para que su contenido quede en `C:\xampp\mysql`.
5. Copia desde `mysql-old` las carpetas `data`, `backup` y `scripts`, el archivo `bin\my.ini` y los `.bat` de la raíz (`mysql_installservice.bat` y `mysql_uninstallservice.bat`).
6. Inicia MySQL desde el panel y ejecuta el actualizador de tablas: `mysql_upgrade.exe` (en las versiones nuevas se llama `mariadb-upgrade.exe`).
7. Reinicia MySQL y revisa que la aplicación abra bien.

Si algo sale mal, borra la carpeta `mysql` nueva, devuelve `mysql-old` a su nombre y listo.

## Instalación en local con XAMPP

Necesitas XAMPP para Windows (con PHP 8.0 o superior, pero ya viste que conviene la última) y Git, este último solo si quieres clonar el repositorio.

**1. Copia el proyecto.** Tiene que quedar en `C:\xampp\htdocs\galezarzal`, con ese nombre exacto y en minúsculas, porque los enlaces internos apuntan a `/galezarzal/`.

```
git clone https://github.com/mihzuk/Galezarzal.git C:\xampp\htdocs\galezarzal
```

Si prefieres bajar el ZIP desde GitHub, descomprímelo y cambia el nombre de la carpeta `Galezarzal-main` por `galezarzal`. Revisa que `index.php` quede directamente dentro de esa carpeta y no en una subcarpeta.

**2. Enciende los servicios.** Abre el Panel de Control de XAMPP e inicia Apache y MySQL.

**3. Crea la base de datos.** Entra a phpMyAdmin (http://localhost/phpmyadmin) y:

1. Haz clic en Nueva, ponle de nombre `galezarzal`, elige el cotejamiento `utf8mb4_unicode_ci` y presiona Crear.
2. Selecciona la base `galezarzal` en el panel de la izquierda. El script no trae `CREATE DATABASE` ni `USE`, así que si lo importas sin tenerla seleccionada te sale el error "No database selected".
3. Ve a la pestaña Importar, elige `database/galezarzalBD.sql` y presiona Importar.
4. Al terminar deberías ver 15 tablas.

Si ya tenías la base importada antes de que existieran las tablas de mensajes de contacto y de recuperación de contraseña, no hace falta rehacerla: ejecuta `database/mensajeContacto.sql` y `database/tokenRecuperacion.sql` en la pestaña SQL, con la base seleccionada.

Si necesitas repetir la importación, borra primero la base desde Operaciones y vuélvela a crear. El script no usa `IF NOT EXISTS`, entonces sobre tablas que ya existen falla con "Table already exists".

**4. Abre el sitio.** Entra a http://localhost/galezarzal/ y deberías ver la página de inicio con el local de ejemplo "el niche store".

La conexión a la base de datos se configura en `includes/config.php`: usuario `root`, sin contraseña y base `galezarzal`.

## Primeros pasos en la aplicación

Los datos de `galezarzalBD.sql` son inventados. El usuario de ejemplo (`demo@example.com`, con rol de administrador) no tiene una contraseña válida, así que no sirve para iniciar sesión. Lo primero es crear tu propia cuenta.

Para eso entra a http://localhost/galezarzal/usuario/ingresarCuenta.php, abre la pestaña Crear cuenta y llena el formulario (la contraseña debe tener mínimo seis caracteres). La cuenta nace con rol de comprador.

Si quieres publicar un local, inicia sesión y usa el botón "tengo un local" en la página de inicio. Al terminar el asistente pasas a ser vendedor, pero tu local queda pendiente y no aparece en el inicio ni en el buscador hasta que un administrador lo apruebe.

**Recuperar la contraseña en local.** Cuando alguien pide restablecer su contraseña, el sitio le manda un enlace por correo. En tu equipo no hay servidor de correo, así que ese correo no llegaría. Para trabajar sin él, copia `includes/config.local.example.php` como `includes/config.local.php` (Git ignora ese archivo, así que no se sube). Con eso el sitio no envía correo: el enlace aparece en pantalla, en un recuadro de "Modo desarrollo", y también queda en `logs/recuperacion.log`. El recuadro solo se muestra si abres el sitio desde el mismo equipo (`localhost`); si lo abres desde el celular u otro computador de la red, busca el enlace en ese archivo de log.

Sin ese archivo, el sitio se comporta como en producción: nunca muestra el enlace en pantalla. Eso es a propósito, porque si se mostrara en un sitio publicado cualquiera podría cambiarle la contraseña a otra persona. No copies `config.local.php` al servidor donde publiques el sitio.

Y como el proyecto no trae ningún administrador utilizable, ese rol hay que asignarlo a mano. En phpMyAdmin, con la base `galezarzal` seleccionada, ve a la pestaña SQL y ejecuta esto cambiando el correo por el de tu cuenta:

```sql
UPDATE usuario SET idRol = 3 WHERE email = 'tu_correo@ejemplo.com';
```

Cierra sesión y vuelve a entrar, porque el rol se guarda en la sesión al ingresar. En el menú de Configuración te va a aparecer la parte de administración, y los locales pendientes los apruebas en http://localhost/galezarzal/admin/moderarLocales.php.

## Si algo falla

- **Error 404, página sin estilos o enlaces rotos.** Casi seguro la carpeta no se llama `galezarzal` o quedó metida dentro de otra. Repasa el primer paso de la instalación.
- **"Error de conexión a la base de datos".** Revisa que MySQL esté encendido, que la base se llame `galezarzal` y que `root` no tenga contraseña. Si la tiene, cambia `DB_PASS` en `includes/config.php`.
- **Errores de sintaxis o `Call to undefined function str_contains()`.** Tu PHP es anterior a la 8.0. Actualiza XAMPP.
- **Apache no arranca porque el puerto 80 está ocupado.** Cambia el puerto en la configuración de XAMPP y entra con `http://localhost:PUERTO/galezarzal/`.

## Lo que Galezarzal no hace (por ahora)

Es una vitrina y un canal de contacto, no una tienda con pagos en línea. La compra se acuerda directamente entre comprador y vendedor por WhatsApp y no hay pasarela de pago.

La recuperación de contraseña funciona con enlaces de un solo uso que vencen a los 60 minutos, pero el envío del correo todavía depende de que el servidor tenga correo configurado: el código usa `mail()`, que en XAMPP no viene listo. Por seguridad, el enlace nunca se muestra en pantalla a menos que actives el modo desarrollo en tu propio equipo (ver "Recuperar la contraseña en local" más arriba).

La guía de instalación está pensada para XAMPP en Windows. En Linux y macOS, que sí distinguen mayúsculas de minúsculas, hay dos pendientes en el código. Por un lado, la carpeta se llama `Local/` pero los enlaces apuntan a `/galezarzal/local/...`, lo que da errores 404. Por otro, las consultas usan nombres de tabla con mayúscula inicial (`Usuario`, `Rol`, `Local`) que no coinciden con las tablas en minúscula del script, a menos que configures `lower_case_table_names=1` en `my.cnf`. En Windows ese valor ya viene activado.

## Equipo

Mantenemos el repositorio [@josdaver3000](https://github.com/josdaver3000), [@Johan-v-p](https://github.com/Johan-v-p), [@mihzuk](https://github.com/mihzuk) y [@lizarazo2005](https://github.com/lizarazo2005).

## Proceso Scrum y documentación

El proyecto se desarrolla con Scrum y tableros Kanban en GitHub Projects. Cómo trabajamos, las ceremonias y las plantillas están en la carpeta `docs/`:

- [Guía de Scrum](docs/scrum.md): roles, ceremonias, tablero, flujo con Git y control de cambios de diseño.
- [Definition of Done](docs/DoD.md) y [Definition of Ready](docs/DoR.md).
- [Requerimientos funcionales](docs/requerimientos/funcionales.md) y [no funcionales](docs/requerimientos/no-funcionales.md).
- Diagramas como código (Mermaid): [entidad-relación](docs/diagramas/er.md), [clases](docs/diagramas/clases.md), [casos de uso](docs/diagramas/casos-de-uso.md), [secuencia](docs/diagramas/secuencia.md) y [arquitectura](docs/diagramas/arquitectura.md).
- [Colaboración entre grupos](docs/colaboracion-entre-grupos.md) y [backlog inicial](docs/backlog-inicial.md).
- Documentos originales de Desarrollo de Software I (ICONIX): [documento funcional](docs/legacy/Documento_Funcional_ICONIX.pdf) y [diagramas draw.io](docs/legacy/Diagramas_ICONIX.drawio).
- Criterios de aceptación en Gherkin: carpeta [`features/`](features/).
- Actas de ceremonias: carpeta [`docs/actas/`](docs/actas/).

Todo cambio que altere el diseño original obliga a actualizar los diagramas y la documentación en el mismo pull request.

## Cómo contribuir

La rama `main` está protegida: nadie sube cambios directo, todo entra con un pull request aprobado. Trabajamos con una tarea por Issue, una rama y un pull request.

1. Elige un Issue del Backlog en el Project, asígnatelo y muévelo a In progress.
2. Actualiza `main` y crea una rama nueva para la tarea:

   ```
   git switch main
   git pull origin main
   git switch -c feature/nombre-de-la-tarea
   ```

   Usamos `feature/...` para funciones nuevas, `fix/...` para correcciones y `docs/...` para documentación.

3. Haz los cambios, pruébalos en XAMPP y sube la rama:

   ```
   git add .
   git commit -m "feat: descripción breve del cambio"
   git push -u origin feature/nombre-de-la-tarea
   ```

4. Abre un pull request hacia `main` y escribe en la descripción `Closes #número-del-issue`.
5. Espera la revisión. Si piden cambios, corrígelos en la misma rama y haz push; el pull request se actualiza solo.
6. Cuando lo aprueben y se una a `main`, borra la rama. Para la siguiente tarea crea una rama nueva (no reutilices la anterior) y siempre después de hacer `git pull` en `main`.
