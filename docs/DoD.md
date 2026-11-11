# Definition of Done (DoD)

Una historia (de usuario o técnica) se considera **terminada** cuando cumple **todo** lo siguiente:

- [ ] Cumple todos los criterios de aceptación en Gherkin del Issue.
- [ ] El código está en un Pull Request, enlazado al Issue (`Closes #N`).
- [ ] El PR fue revisado y aprobado por al menos una persona del grupo dueño (Code Owner).
- [ ] Se probó manualmente cada escenario Gherkin y se describió cómo en el PR.
- [ ] No hay credenciales, claves ni datos personales en el código ni en el SQL.
- [ ] Si altera el diseño original: diagramas (`docs/diagramas/`), requerimientos (`docs/requerimientos/`), escenarios (`features/`) y `database/galezarzalBD.sql` están actualizados **en el mismo PR**.
- [ ] El código sigue la estructura de módulos existente (`<módulo>/` en la raíz del repo).
- [ ] La rama quedó fusionada en `main` y el Issue cerrado.
- [ ] La tarjeta está en **Done** en el tablero.

## DoD de un Sprint
- [ ] Existe el acta de Sprint Planning con el Sprint Goal y todo el Sprint Backlog, subida **antes** de empezar el sprint.
- [ ] El incremento funciona en `main` y puede ejecutarse siguiendo el README.
- [ ] Sprint Review realizada y acta subida a `docs/actas/`, comparando contra el acta de Planning.
- [ ] Retrospectiva realizada y acta subida a `docs/actas/`.
