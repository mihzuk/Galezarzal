# Acta de Sprint Review, Sprint 2

Acá comparamos lo que nos comprometimos a hacer en el acta de Sprint Planning con lo que realmente se entregó.

| | |
|---|---|
| **Sprint** | 2 |
| **Fecha y hora** | 2026-11-19, de 9:00 p. m. a 9:45 p. m. 
| **Scrum Master y Product Owner** | Cristian Felipe Arango Gómez (Scrum Master), Michael Calderón (Product Owner) |
| **Asistentes (y el profe, si asiste)** | Cristian Felipe Arango Gómez, Divis Smith Ramírez, Michael Calderón. 
Faltó sin justificación: Juan Manuel Cardona. |

## Sprint Goal

Lo copiamos del acta de Planning: que los compradores y administradores puedan configurar y al salir de la página web se guarde.

**¿Se cumplió?** Sí. Todos los issues comprometidos se cumplieron. Las preferencias de privacidad y de notificaciones del comprador (#6) y la configuración de la plataforma del administrador (#7) se guardan en la base de datos y se mantienen después de cerrar sesión y volver a entrar.

## Sprint Backlog: comprometido contra entregado

| Issue | Título | Estado final | Cumple el DoD | Observaciones |
|---|---|---|---|---|
| #6 | Guardar las preferencias de privacidad y de notificaciones | Done | Sí, con el PR #42 | PR #38, aprobado por lizarazo2005 y fusionado el 2026-11-17. La tabla `preferencias_usuario` faltaba en el diagrama ER; se agrega en el PR #42. |
| #7 | Guardar la configuración de la plataforma | Done | Sí | PR #14, aprobado por josdaver3000 y fusionado el 2026-11-17. El PR usaba `Refs #7` en vez de `Closes #7`, así que la issue se cerró a mano. El modo mantenimiento, la aprobación manual y el nombre del sitio se guardan pero todavía no se aplican: pasan a la HU nueva #41. |
| #8 | Marcar productos como favoritos | Done | Sí | PR #40, aprobado por lizarazo2005. Se fusionó el 2026-11-18 a las 00:07, 7 minutos después de terminar el sprint; el PO la acepta dentro del Sprint porque el trabajo se hizo durante el sprint. Pasó de Valentina a Divis el 2026-11-17. |

**Story points comprometidos:** 16
**Story points terminados** (esta es la velocidad que se usa para la capacidad del próximo Planning): 16

## Avance hacia el Product Goal

Qué tan cerca estamos y si hay que ajustar algo: ya funcionan las preferencias (#6) y los favoritos de productos (#8). La configuración se guarda (#7), pero solo se aplica el registro abierto o cerrado; el resto queda en #41. Faltan las notificaciones (#33), el contacto (#35) y publicar el sitio en línea (épica #5).

## Demostración

Qué mostramos y cómo (pasos, usuario de prueba, enlace al PR o al release): release [v1.1-sprint-1](https://github.com/mihzuk/Galezarzal/releases/tag/v1.1-sprint-1).

- **Preferencias (#6, PR #38):** como comprador, cambiar notificaciones y privacidad, cerrar sesión, volver a entrar y comprobar que siguen igual.
- **Configuración (#7, PR #14):** como administrador, guardar un parámetro y recargar; apagar "Registro abierto" y comprobar que no se puede crear una cuenta.
- **Favoritos (#8, PR #40):** agregar un producto a favoritos, verlo en la lista y quitarlo.

## Retroalimentación

Qué mejorar para el próximo sprint:
- Cumplir los horarios.
- No abandonar el grupo.
- Ser responsables.
- Comentar en GitHub.

Compromisos:
- **Divis:** se compromete a cumplir antes de las 12.
- **Cristian Felipe Arango Gómez:** se compromete a pasar el enlace de Meet por correo.

## Cambios al Product Backlog después de la review

Issues nuevos o con otra prioridad: #41 (nueva, épica #3, prioridad Media), #43 (nueva, Docker, épica #5, prioridad Alta), #24 (ahora es elegir dónde publicar los contenedores). La épica #3 sigue abierta hasta terminar la #41.

## Cambios de diseño en el sprint

- [x] Hubo cambios de diseño y los diagramas y la documentación quedaron actualizados (enlazar el PR o el Issue). PR #14 (tabla `configuracion`), PR #38 (tabla `preferencias_usuario`) y PR #42 (diagrama ER).
- [ ] No hubo cambios de diseño.