# Cómo trabajamos

Desarrollamos el proyecto con Scrum y llevamos el tablero Kanban en GitHub Projects. Lo de DevOps lo vamos agregando a medida que lo veamos en clase.

## Dónde queda cada cosa

| Qué | Dónde |
|---|---|
| Product Backlog | GitHub Project, vista Table y columna Backlog |
| Sprint Backlog | Columna Sprint Backlog y el campo Sprint |
| Épicas, historias de usuario, historias técnicas y bugs | Issues (plantillas en `.github/ISSUE_TEMPLATE/`) |
| Requerimientos funcionales y no funcionales | `docs/requerimientos/` y Issues con la etiqueta `RF` o `RNF` |
| Criterios de aceptación | Gherkin dentro del Issue y en `features/` |
| DoD y DoR | `docs/DoD.md` y `docs/DoR.md` |
| Product Goal | Sección "Artefactos y sus compromisos" de este documento |
| Actas | `docs/actas/` |
| Diagramas | Código Mermaid en `docs/diagramas/` |
| Cambios de diseño | Pull Request con el checklist de la plantilla |

## Roles

- **Product Owner:** es el dueño del backlog. Define y prioriza los requerimientos y acepta o rechaza el trabajo.
- **Scrum Master:** se encarga de que el proceso se cumpla, convoca las ceremonias, cuida sus tiempos y ayuda a quitar los impedimentos.
- **Equipo de desarrollo:** se organiza solo. Decide cuánto trabajo cabe en el sprint y quién toma cada tarjeta, y estima, construye y entrega un incremento que cumpla el DoD.

### Cómo repartimos los roles entre los dos grupos

Cada grupo es dueño de su proyecto y desarrolla el del otro, así que cada uno tiene dos roles:

- En el proyecto propio hay un Product Owner. Los demás son Code Owners: revisan y aprueban los Pull Requests y ayudan al PO a aclarar el backlog.
- En el proyecto del otro grupo hay un Scrum Master y los demás son desarrolladores.

Las reglas son:

- Hay un solo PO por proyecto.
- El PO y el Scrum Master no pueden ser la misma persona.
- El Scrum Master también programa.
- El Scrum Master es fijo durante todo el proyecto: no rota entre sprints. Si algún día hay que cambiarlo, se registra en "Cambios en el equipo" con la fecha y el motivo.

### Grupo Galezarzal

| Integrante | En Galezarzal | En Agora |
|---|---|---|
| Michael Calderon | Product Owner | Desarrollador |
| Johan Vallecilla | Code Owner | Desarrollador |
| José David Vergara | Code Owner | Scrum Master (fijo) |
| Carlos Lizarazo | Code Owner | Desarrollador |

### Grupo Agora

| Integrante | En Agora | En Galezarzal |
|---|---|---|
| Divis | Product Owner | Desarrollador |
| Cristian | Code Owner | Scrum Master (fijo) y desarrollador |
| Juan Cardona | Code Owner | Desarrollador |

El equipo que desarrolla Galezarzal queda con tres personas: Cristian (Scrum Master), Divis y Juan Cardona.

Falta confirmar con el profe si el PO y el Scrum Master de cada proyecto salen del grupo dueño o del que desarrolla, y quién asiste a cada ceremonia. Si responde otra cosa, ajustamos este reparto.

### Cambios en el equipo

Cada cambio en el equipo se registra acá, sin borrar los anteriores. Lo aprueba el Product Owner, y el Scrum Master lo deja también en el acta de Planning del sprint en curso.

| Fecha | Cambio | Motivo | Qué se hizo con su trabajo |
|---|---|---|---|
| 2026-11-17 | Valentina deja el equipo. En Galezarzal era desarrolladora y en Agora era Code Owner. | Pasó a trabajar en otro proyecto. | La #8 (Marcar productos como favoritos, 6 puntos) sigue en el Sprint 1 y pasa a Divis. Se le quitó el acceso al repositorio y al tablero. |
| 2026-11-17 | El Scrum Master deja de rotar. Cristian queda fijo en Galezarzal y José David queda fijo en Agora. | Decisión de los dos grupos. Con la salida de Valentina, la rotación ya no se podía cumplir. | Se quitó la rotación (Valentina y Juan Cardona en Galezarzal; Johan y Carlos en Agora). |

## Artefactos y sus compromisos

Cada artefacto de Scrum tiene un compromiso, que es contra lo que se mide el avance:

| Artefacto | Compromiso | Dónde está | Quién responde |
|---|---|---|---|
| Product Backlog | Product Goal | GitHub Project, vista Table | Product Owner |
| Sprint Backlog | Sprint Goal | Columna Sprint Backlog y acta de Planning | Equipo de desarrollo |
| Incremento | Definition of Done | `main`, con un tag o release por sprint, y `docs/DoD.md` | Equipo de desarrollo |

**Product Goal:** que los comerciantes de la Galería de Zarzal tengan el marketplace publicado en línea, con todo lo que el sitio ya muestra funcionando de verdad: las preferencias, la configuración, los favoritos de productos, las notificaciones y el contacto.

El Product Goal lo define el Product Owner y solo él lo cambia. Cada Sprint Goal tiene que acercar al equipo a este objetivo.

## Sprint

- Cada sprint dura **2 semanas**, como lo confirmó el profe. Empieza un miércoles y termina el martes de la semana siguiente a las 12 de la noche, igual que el campo Sprint del tablero. La duración no cambia a mitad del proyecto.
- Sprint 1: del 2026-11-04 al 2026-11-17. Sprint 2: del 2026-11-18 al 2026-12-01. Sprint 3: del 2026-12-02 al 2026-12-15.
- Durante el sprint no se hacen cambios que pongan en riesgo el Sprint Goal. El alcance se puede aclarar con el PO, y todo cambio queda en la tabla de cambios del acta de Planning.
- Solo el Product Owner puede cancelar un sprint, y solo si el Sprint Goal deja de tener sentido.

### Capacidad del equipo

La capacidad se calcula en cada Planning con las personas que van a estar disponibles en el sprint. Como referencia se toma lo que el equipo terminó en el sprint anterior (la velocidad). Desde el Sprint 2 el equipo de desarrollo es de tres personas, así que la capacidad se estima a partir de eso y no de los 16 puntos del Sprint 1, que se calcularon con cuatro.

## Ceremonias

Los tiempos máximos son los de la Guía Scrum para un sprint de 2 semanas.

| Ceremonia | Cuándo | Tiempo máximo | Asisten | Qué se define | Evidencia |
|---|---|---|---|---|---|
| Sprint Planning | Al empezar el sprint | 4 horas | PO, Scrum Master y equipo de desarrollo | Por qué (Sprint Goal), qué (Sprint Backlog) y cómo | Acta (`_plantilla-sprint-planning.md`) y los Issues movidos a Sprint Backlog |
| Daily | En los horarios acordados en el acta de Planning | 15 minutos | Equipo de desarrollo y Scrum Master | Avance hacia el Sprint Goal, el plan hasta la próxima Daily y lo que bloquea | Acta corta (`_plantilla-daily.md`) o comentarios en los Issues |
| Refinamiento | Durante el sprint | Hasta el 10 % del tiempo del equipo | PO y equipo de desarrollo | Detallar y estimar las historias que vienen | Acta (`_plantilla.md`) e Issues actualizados que cumplan el DoR |
| Sprint Review | Al terminar el sprint | 2 horas | PO, Scrum Master, equipo de desarrollo y el profe | Mostrar el incremento, compararlo con lo comprometido y ajustar el Product Backlog | Acta (`_plantilla-sprint-review.md`) y un tag o release |
| Retrospectiva | Después de la Review, antes del siguiente Planning | 1 hora y 30 minutos | Scrum Master, equipo de desarrollo y PO | Qué mejorar en la forma de trabajar | Acta (`_plantilla-retrospectiva.md`) con los compromisos |

El Scrum Master convoca todas las ceremonias y se asegura de que duren lo acordado. Como la Daily es flexible por los horarios de trabajo del equipo, sus horarios se acuerdan en cada Planning y se anotan en el acta.

El profe pidió que cada Sprint Planning tenga un acta clara con el Sprint Goal y todo el Sprint Backlog, porque la revisa en cada Sprint Review. Por eso el acta de la Review siempre se compara con la de Planning. Si algo cambia a mitad del sprint, se anota en la tabla de cambios del acta de Planning sin borrar lo que había.

También quedó claro que cualquier entrega de información o de requerimientos se hace de forma formal, con un acta o con el Issue que corresponda. Lo que se hable solo por chat no cuenta hasta que quede en GitHub.

## Tablero

Las columnas son Backlog, Sprint Backlog, In Progress, In Review y Done. Los campos que usamos son Sprint, Story Points, Prioridad y Tipo (épica, historia de usuario, historia técnica, RF, RNF o bug).

- Una tarjeta pasa a In Progress cuando alguien crea su rama.
- Pasa a In Review cuando se abre el Pull Request.
- Pasa a Done cuando el PR se fusiona (con `Closes #N` se mueve sola).
- Cada persona trabaja máximo en dos tarjetas a la vez.

## Cómo trabajamos con Git

1. Cada Issue se trabaja en su rama: `feature/12-reserva`, `fix/15-precio`, `docs/20-actas`.
2. Commits pequeños y con mensajes que se entiendan, por ejemplo `feat(catalogo): cambiar estado del producto (#14)`.
3. Pull Request con la plantilla y enlazado al Issue.
4. Lo revisa y lo aprueba un Code Owner. En `main` no se hacen commits directos.

## Cambios de diseño

Todo cambio que altere el diseño original obliga a actualizar los diagramas y la documentación. Lo hacemos así:

1. Si un Issue o un PR cambia el diseño, se marca la casilla "Altera el diseño original" en el PR y se enlaza un Issue de tipo Cambio de diseño.
2. El PR no se aprueba hasta que los diagramas, los requerimientos, el Gherkin y `database/galezarzalBD.sql` afectados estén actualizados en ese mismo PR.
3. El historial de Git queda como prueba de cada cambio y de por qué se hizo.

## Convenciones

- Los Issues y la documentación van en español.
- Los identificadores son `RF-XX` y `RNF-XX`. Del RF-01 al RF-24 y del RNF-01 al RNF-07 ya existen (vienen del documento funcional); los nuevos siguen la numeración.
- La línea base es el estado del proyecto cuando empezamos con Scrum, marcada con el tag `v1.0-baseline`.
