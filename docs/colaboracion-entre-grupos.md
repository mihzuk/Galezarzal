# Colaboración entre grupos

> **Dinámica:** cada grupo entrega los requerimientos al otro. El grupo autor de un proyecto escribe los requerimientos nuevos que el otro grupo desarrollará. Falta validar con el docente el detalle de los roles de Scrum entre grupos; ajusta este documento cuando se confirme.

## Modelo de trabajo
Cada grupo cumple **dos papeles a la vez**: dueño de su propio repositorio y desarrollador del repositorio del otro grupo.

- **Grupo dueño** (autor del repositorio): actúa como *Product Owner*. Escribe los requerimientos nuevos para el otro grupo (épicas e historias con sus criterios Gherkin), mantiene el backlog, prioriza y **aprueba** los Pull Requests.
- **Grupo desarrollador** (el que recibe el proyecto): actúa como equipo de desarrollo. Toma Issues del Sprint, trabaja en ramas o en un *fork*, y abre Pull Requests.
- El docente revisa épicas, historias, requerimientos, Gherkin, DoD, diagramas y actas.

## Cómo se entrega un requerimiento
1. El grupo dueño abre el Issue con la plantilla correspondiente (épica, historia o **Solicitud de requerimiento**). El grupo desarrollador puede abrir una Solicitud para proponer o aclarar algo, pero decide el dueño.
2. El Product Owner lo refina hasta cumplir el DoR (`docs/DoR.md`).
3. Entra al Product Backlog y se prioriza.
4. Se planifica en un Sprint (acta de Planning).

Nada se considera entregado si solo se dijo por chat.

## Cómo se entrega trabajo
1. Rama o fork por Issue → Pull Request con la plantilla.
2. Si el cambio altera el diseño: diagramas, documentación, Gherkin y `database/galezarzalBD.sql` se actualizan en el mismo PR.
3. Un Code Owner del grupo dueño aprueba y fusiona.
4. El Issue se cierra y la tarjeta pasa a *Done*.

## Reglas de convivencia
- Toda comunicación con efecto sobre el proyecto queda en Issues, PRs o actas.
- Los desacuerdos de alcance se resuelven en el Issue; si no, se escalan al docente.
- Cada grupo cumple la ceremonia de Review mostrando lo hecho sobre el repositorio del otro.
