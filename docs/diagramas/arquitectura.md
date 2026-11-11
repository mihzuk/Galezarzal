# Arquitectura y paquetes

Aplicación web en **PHP** (sin framework) sobre **MySQL/MariaDB**, servida con Apache (XAMPP en desarrollo). Cada carpeta de la raíz del repositorio es un módulo.

```mermaid
flowchart TB
    B[Navegador: móvil o escritorio]

    subgraph WEB[Servidor web Apache + PHP]
        direction TB
        IDX[index.php · Vitrina pública]
        subgraph MOD[Módulos]
            direction LR
            USR[usuario/<br/>registro, login,<br/>cuenta]
            LOC[Local/<br/>crear, ver,<br/>buscar, mi local]
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
            SEG[csrf.php · validaciones.php<br/>recuperacion.php]
        end
        ASSETS[assets/css/style.css]
    end

    DB[(MySQL<br/>15 tablas)]

    B -->|HTTP| IDX
    B -->|HTTP| MOD
    MOD --> INC
    IDX --> INC
    INC -->|PDO| DB
    MOD --> ASSETS
```

## Decisiones vigentes
- Las rutas internas usan el prefijo `/galezarzal/`: la carpeta publicada en Apache debe llamarse `galezarzal` (ver README).
- Roles: Comprador, Vendedor y Administrador (tabla `rol`); el visitante es un usuario sin sesión.
- Sin pasarela de pago: la compra se concreta entre comprador y vendedor (WhatsApp o presencial).
