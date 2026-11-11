# Cómo trabajamos: Scrum + Kanban en GitHub

El proyecto se desarrolla con **Scrum**, con tableros **Kanban en GitHub Projects**.
Las prácticas de **DevOps** se irán incorporando a medida que se vean en clase.

## Dónde vive cada cosa

| Elemento de Scrum | Dónde está en GitHub |
|---|---|
| Product Backlog | GitHub Project (vista *Table* y columna *Backlog*) |
| Sprint Backlog | Columna *Sprint Backlog* + campo *Sprint* |
| Épicas, historias de usuario, historias técnicas | **Issues** (plantillas en `.github/ISSUE_TEMPLATE/`) |
| Requerimientos funcionales / no funcionales | `docs/requerimientos/` + Issues con etiqueta `RF` / `RNF` |
| Criterios de aceptación | Gherkin dentro del Issue y en `features/*.feature` |
| DoD / DoR | `docs/DoD.md`, `docs/DoR.md` |
| Actas de ceremonias | `docs/actas/` (plantilla en `_plantilla.md`) |
| Diagramas | Código Mermaid en `docs/diagramas/` |
| Cambios de diseño | Pull Requests con el checklist de la plantilla |

## Roles
- **Product Owner (PO):** dueño del backlog; define y prioriza requerimientos, acepta el trabajo.
- **Scrum Master (SM):** facilita el proceso, convoca ceremonias, elimina impedimentos.
- **Equipo de desarrollo:** estima, construye y entrega el incremento.

> Define aquí los nombres de las personas en cada rol:
> PO: ___  ·  SM: ___  ·  Desarrollo: ___

## Ceremonias y evidencia formal

| Ceremonia | Cuándo | Qué se decide | Evidencia |
|---|---|---|---|
| Sprint Planning | Inicio del sprint | **Sprint Goal** y **todo el Sprint Backlog** | Acta (`_plantilla-sprint-planning.md`) + Issues movidos a *Sprint Backlog* |
| Daily Scrum (≤15 min) | Cada día de trabajo | Qué hice, qué haré, qué me bloquea | Acta breve semanal o comentarios en Issues |
| Refinamiento | Durante el sprint | Detallar y estimar historias futuras | Issues actualizados (cumplen DoR) |
| Sprint Review | Fin del sprint | Comparar lo comprometido con lo entregado, demostrar el incremento y recibir feedback | Acta (`_plantilla-sprint-review.md`) + tag/release |
| Retrospectiva | Tras la Review | Qué mejorar del proceso | Acta con acuerdos de mejora |

**Regla del docente:** cada Sprint Planning debe tener un acta clara que indique el **Sprint Goal** y **todo el Sprint Backlog**. El docente revisa esa acta en cada Sprint Review, así que el acta de Review siempre la referencia y compara contra ella. Si un cambio ocurre en mitad del sprint, se registra en la tabla de cambios del acta de Planning, sin borrar lo original.

Toda entrega de información o de requerimientos se hace **formalmente**: con un acta o con el elemento correspondiente en el repositorio (Issue). Lo que se acuerda solo por chat no existe hasta que queda en GitHub.

## Tablero Kanban

Columnas: `Backlog` → `Sprint Backlog` → `In Progress` → `In Review` → `Done`.

Campos personalizados: **Sprint** (Iteration), **Story Points**, **Prioridad**, **Tipo** (Épica / HU / HT / RF / RNF / Bug).

Reglas:
- Una tarjeta pasa a *In Progress* cuando alguien crea su rama.
- Pasa a *In Review* cuando se abre el PR.
- Pasa a *Done* cuando el PR se fusiona (automático con `Closes #N`).
- Límite sugerido de trabajo en curso: máximo 2 tarjetas *In Progress* por persona.

## Flujo de trabajo con Git

1. Cada Issue se trabaja en su rama: `feature/<n>-descripcion-corta`, `fix/<n>-...`, `docs/<n>-...`.
2. Commits pequeños y descriptivos (ej.: `feat(catalogo): cambiar estado de producto (#14)`).
3. Pull Request con la plantilla, enlazado al Issue.
4. Revisión y aprobación por un Code Owner. `main` está protegida: no se hacen commits directos.

## Control de cambios de diseño

> **Todo cambio que altere el diseño original obliga a actualizar los diagramas y la documentación.**

Se garantiza así:
1. Si un Issue o PR altera el diseño, se marca la casilla "Altera el diseño original" y se abre (o se enlaza) un Issue de tipo **Cambio de diseño**.
2. El PR **no se aprueba** hasta que diagramas, requerimientos, Gherkin y `database/galezarzalBD.sql` afectados estén actualizados en el mismo PR.
3. El historial de Git deja constancia de cada cambio y su justificación.

## Convenciones
- Idioma de Issues y documentación: español.
- Identificadores: `RF-XX`, `RNF-XX`. Los números ya usados (RF-01 a RF-24, RNF-01 a RNF-07) pertenecen a la línea base; los nuevos continúan la numeración.
- Línea base: el estado documentado en `docs/` al iniciar el proceso Scrum (tag `v1.0-baseline`).
