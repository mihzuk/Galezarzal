# Arquitectura y paquetes

Es una aplicación web en PHP, sin framework, sobre MySQL o MariaDB y servida con Apache (XAMPP mientras desarrollamos). Cada carpeta de la raíz del repositorio es un módulo.

Las rutas de la aplicación se configuran de forma centralizada mediante `APP_URL` y `BASE_PATH`, definidos en `includes/config.php` (Issue #10).

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
            CONF[config.php<br/>APP_URL y BASE_PATH<br/>conexión PDO]
            AUT[aut_verifica.php<br/>sesión y roles]
            HF[header.php · footer.php]
            ERR[aut_visualizar_error.php]
            SEG[csrf.php · validaciones.php<br/>recuperacion.php · correo.php]
            PLAT[configuracion.php · mantenimiento.php<br/>parámetros de la plataforma]
        end

        ASSETS[assets/css/style.css]
    end

    URL[Configuración de URL base]
    DB[(MySQL<br/>15 tablas)]

    B -->|HTTP| IDX
    B -->|HTTP| MOD
    URL -->|APP_URL| CONF
    MOD --> INC
    IDX --> INC
    CONF -.->|BASE_PATH para enlaces| MOD
    CONF -.->|BASE_PATH para enlaces| IDX
    INC -->|PDO| DB
    MOD --> ASSETS
```

## Configuración de rutas

La aplicación utiliza dos constantes definidas en `includes/config.php`:

- `APP_URL`: dirección base de la aplicación.
- `BASE_PATH`: ruta base obtenida de `APP_URL` para construir enlaces internos y redirecciones.

Esto permite instalar Galezarzal en carpetas con distintos nombres sin modificar manualmente los enlaces de cada módulo.

### Ejemplos

| Instalación | APP_URL | BASE_PATH |
|---|---|---|
| Carpeta original | `http://localhost/galezarzal` | `/galezarzal` |
| Otra carpeta | `http://localhost/mitienda` | `/mitienda` |
| Dominio propio | `https://ejemplo.com` | Cadena vacía |

## Requerimientos relacionados

- **RNF-02 — Disponibilidad:** la configuración facilita cambiar la ubicación de despliegue, pero no garantiza el 95 % de disponibilidad.
- **RNF-05 — Escalabilidad:** la centralización de rutas permite trasladar la aplicación sin modificar los enlaces de cada módulo.

## Cosas que tener en cuenta

- Las rutas internas utilizan `BASE_PATH`, por lo que la carpeta en Apache no tiene que llamarse obligatoriamente `galezarzal`.
- Los roles son comprador, vendedor y administrador (tabla `rol`). El visitante es simplemente alguien sin sesión.
- No hay pasarela de pago: la compra se acuerda entre comprador y vendedor por WhatsApp o en persona.
- La compatibilidad completa con servidores Linux y Windows se trabaja por separado en RNF-11.
