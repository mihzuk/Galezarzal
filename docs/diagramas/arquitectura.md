# Arquitectura y paquetes

Es una aplicación web en PHP, sin framework, sobre MySQL o MariaDB y servida con Apache. Para el desarrollo local se puede utilizar XAMPP o Docker Compose (Issue #43). Cada carpeta de la raíz del repositorio es un módulo.

```mermaid
flowchart TB
    B[Navegador: móvil o escritorio]

    subgraph WEB[Servidor web Apache + PHP]
        direction TB
        IDX[index.php · Vitrina pública]
        subgraph MOD[Módulos]
            direction LR
            USR[usuario/<br/>registro, login,<br/>cuenta]
            LOC[local/<br/>crear, ver,<br/>buscar, mi local]
            CAT[catalogo/<br/>producto: ver,<br/>agregar, modificar]
            FAV[favoritos/]
            NOT[notificaciones/]
            CFG[configuracion/<br/>perfil, local,<br/>privacidad]
            ADM[admin/<br/>usuarios, moderación,<br/>plataforma, mensajes]
            AYU[ayudas/<br/>FAQ y contacto]
        end
        subgraph INC[includes/ · núcleo compartido]
            direction LR
            CONF[config.php<br/>conexión PDO]
            AUT[aut_verifica.php<br/>sesión y roles]
            HF[header.php · footer.php]
            ERR[aut_visualizar_error.php]
            SEG[csrf.php · validaciones.php<br/>recuperacion.php · correo.php]
            SMTP[smtp.php<br/>envío por SMTP o mail]
            PLAT[configuracion.php · mantenimiento.php<br/>parámetros de la plataforma]
        end
        ASSETS[assets/css/style.css]
    end

    DB[(MySQL o MariaDB<br/>17 tablas)]

    B -->|HTTP| IDX
    B -->|HTTP| MOD
    MOD --> INC
    IDX --> INC
    INC -->|PDO| DB
    MOD --> ASSETS
```

## Arquitectura del entorno Docker

El entorno de desarrollo utiliza Docker Compose para gestionar dos servicios independientes: `web` y `db`. Esto permite tener un entorno de ejecución uniforme sin reemplazar la instalación tradicional con XAMPP.

```mermaid
flowchart TB
    B[Navegador]
    ENV["Archivo .env"]
    SQL["database/galezarzalBD.sql"]

    subgraph DOCKER[Docker Compose]
        subgraph WEB[Servicio web]
            PHP[PHP 8.3 + Apache]
            APP[Galezarzal]
            PDO[Extensión pdo_mysql]

            PHP --> APP
            APP --> PDO
        end

        subgraph DB[Servicio db]
            MYSQL[(MySQL 8.4<br/>17 tablas)]
        end

        VOL[(Volumen mysql_data)]

        PDO -->|Conexión interna: 3306| MYSQL
        MYSQL -->|Almacenamiento persistente| VOL
    end

    B -->|HTTP: 8080| PHP
    ENV -->|Variables de entorno| PHP
    ENV -->|Credenciales| MYSQL
    SQL -->|Inicialización automática| MYSQL
```

### Componentes del entorno Docker

- `Dockerfile`: define la imagen de PHP 8.3 con Apache y la extensión `pdo_mysql`.
- `docker-compose.yml`: configura los servicios web y db, la red interna y el volumen de almacenamiento.
- `.env.example`: contiene un ejemplo de las variables de entorno necesarias.
- `.env`: contiene las credenciales locales y está excluido del repositorio mediante `.gitignore`.
- `.dockerignore`: evita copiar archivos innecesarios y configuraciones locales a la imagen Docker.
- `database/galezarzalBD.sql`: inicializa automáticamente la base de datos cuando se crea el volumen por primera vez.
- `mysql_data`: volumen que permite conservar la información al detener o recrear los contenedores.

### Comunicación entre servicios

El navegador accede a la aplicación mediante el puerto 8080 del equipo anfitrión. El servicio web se comunica con MySQL utilizando el nombre del servicio `db` y el puerto interno 3306.

El puerto de MySQL no se publica en el equipo anfitrión, evitando conflictos con la instalación local de XAMPP.

### Estado de integración

Durante el desarrollo del Issue #43 se realizaron las siguientes pruebas:

- Construcción de la imagen PHP 8.3 con Apache.
- Inicio de los servicios web y db.
- Importación automática de las 17 tablas.
- Verificación de los datos de ejemplo.
- Conexión directa entre PHP y MySQL mediante PDO.
- Conservación de los datos existentes al eliminar y recrear los contenedores.
- Carga de la página principal desde XAMPP.

La integración de las credenciales de la base de datos en la aplicación depende del Issue #18. Las pruebas del catálogo y la persistencia de cuentas nuevas desde Docker quedan pendientes hasta completar dicha integración.

## Cosas que tener en cuenta

- Las rutas internas usan el prefijo `/galezarzal/`, así que la carpeta en Apache tiene que llamarse `galezarzal` (está en el README).
- En Docker, el proyecto se encuentra en `/var/www/html/galezarzal`.
- Los roles son comprador, vendedor y administrador (tabla `rol`). El visitante es simplemente alguien sin sesión.
- No hay pasarela de pago: la compra se acuerda entre comprador y vendedor por WhatsApp o en persona.
- Docker y XAMPP utilizan bases de datos independientes.
- No se debe subir el archivo `.env` al repositorio.
- No se debe ejecutar `docker compose down -v` si se desea conservar la información de la base de datos.
