# Acta de Sprint Review, Sprint 1

Acá comparamos lo que nos comprometimos a hacer en el acta de Sprint Planning con lo que realmente se entregó.

| | |
|---|---|
| Sprint | 1 (del 2026-11-04 al 2026-11-17) |
| Fecha y hora | AAAA-MM-DD, de HH:MM a HH:MM *(llenar en la reunión)* |
| Acta de Planning de este sprint | `docs/actas/2026-11-14-sprint-1-planning.md` |
| Scrum Master y Product Owner | Cristian Felipe Arango Gómez (Scrum Master), Michael Calderon (Product Owner) |
| Asistentes | *(llenar en la reunión)* |

## Sprint Goal

Lo copiamos del acta de Planning: que los compradores y administradores puedan configurar y al salir de la página web se guarde.

¿Se cumplió? Sí *(confirmar en la reunión)*. Por qué: las preferencias de privacidad y de notificaciones del comprador (#6) y la configuración de la plataforma del administrador (#7) se guardan en la base de datos y se mantienen después de cerrar sesión y volver a entrar.

## Sprint Backlog: comprometido contra entregado

| Issue | Título | Estado final | Cumple el DoD | Observaciones |
|---|---|---|---|---|
| #6 | Guardar las preferencias de privacidad y de notificaciones | Done | Sí, con el PR #42 | PR #38, aprobado por lizarazo2005 y fusionado el 2026-11-17. La tabla `preferencias_usuario` faltaba en el diagrama ER; se agrega en el PR #42. |
| #7 | Guardar la configuración de la plataforma | Done | Sí | PR #14, aprobado por josdaver3000 y fusionado el 2026-11-17. El PR usaba `Refs #7` en vez de `Closes #7`, así que la issue se cerró a mano. El modo mantenimiento, la aprobación manual y el nombre del sitio se guardan pero todavía no se aplican: pasan a la HU nueva #41. |
| #8 | Marcar productos como favoritos | Done | Sí | PR #40, aprobado por lizarazo2005. Se fusionó el 2026-11-18 a las 00:07, 7 minutos después de terminar el sprint; el PO la acepta dentro del Sprint 1 porque el trabajo se hizo durante el sprint. Pasó de Valentina a Divis el 2026-11-17. El PR dejó los diagramas como pendientes, pero el ER, el de clases y el de casos de uso ya incluían los favoritos de productos y la base no cambió. |

Story points comprometidos: 16

Story points terminados (esta es la velocidad que se usa para la capacidad del próximo Planning): 16

## Avance hacia el Product Goal

Qué tan cerca estamos y si hay que ajustar algo:

| Parte del Product Goal | Estado |
|---|---|
| Preferencias | Listo (#6) |
| Configuración | Se guarda (#7). Solo se aplica el registro abierto o cerrado; el resto queda en #41. |
| Favoritos de productos | Listo (#8) |
| Notificaciones | Pendiente: las notificaciones nunca se generan (#33). |
| Contacto | Pendiente: enviar por correo los mensajes de contacto (#35). |
| Publicado en línea | Pendiente: épica #5. Se agregó la HT de Docker (#43) y el #24 ahora es elegir dónde publicar los contenedores. |

## Demostración

Qué mostramos y cómo (pasos, usuario de prueba, enlace al PR o al release):

1. **Preferencias (#6, PR #38):** iniciar sesión como comprador, cambiar una opción en Configuración → Notificaciones y otra en Configuración → Privacidad, guardar, cerrar sesión, volver a entrar y comprobar que siguen como se dejaron.
2. **Configuración de la plataforma (#7, PR #14):** como administrador, cambiar un parámetro en `admin/configurarPlataforma.php`, guardar y recargar. Después apagar "Registro abierto", cerrar sesión y comprobar que `usuario/crearUsuario.php` muestra el aviso de registro cerrado y no deja crear la cuenta.
3. **Favoritos de productos (#8, PR #40):** sin sesión, pulsar "Agregar a favoritos" en un producto y comprobar que pide iniciar sesión. Con sesión, agregarlo, verlo en la lista de favoritos y quitarlo.

Release del sprint: [`v1.1-sprint-1`](https://github.com/mihzuk/Galezarzal/releases/tag/v1.1-sprint-1), sobre el commit `841ab6f` de `main` (fusión del PR #40), que incluye #6, #7 y #8.

## Retroalimentación

- *(llenar en la reunión)*

## Cambios al Product Backlog después de la review

Issues nuevos o con otra prioridad: #41, #43, #24

- **#41 (nueva):** aplicar en el sitio el modo mantenimiento, la aprobación manual y el nombre del sitio. Es parte de la épica #3, con prioridad Media. Falta que el equipo la estime. Para probar la aprobación manual primero hay que resolver el bug #31 (los locales pendientes no aparecen y no se pueden aprobar).
- **#43 (nueva):** entorno de desarrollo con Docker, en la épica #5 con prioridad Alta. Se hace junto con el #18 y desbloquea el #28 y el #29.
- **#24 (cambia):** ahora es elegir dónde publicar los contenedores, con un hosting compartido de PHP como plan B.
- **Épica #3:** sigue abierta (2 de 3). #6 y #7 están terminadas; se agregó la #41 porque el objetivo de la épica pide que la configuración "se respete después", y hoy solo se aplica el registro abierto o cerrado. El criterio de la épica se actualizó para incluirlo.

## Cambios de diseño en el sprint

- [x] Hubo cambios de diseño y los diagramas y la documentación quedaron actualizados (enlazar el PR o el Issue).
  - Tabla `configuracion` (#7, PR #14): SQL, ER, requerimientos y Gherkin actualizados.
  - Tabla `preferencias_usuario` (#6, PR #38): SQL y Gherkin actualizados; el ER se completa en el PR #42.
- [ ] No hubo cambios de diseño.
