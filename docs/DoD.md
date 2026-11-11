# Definition of Done

Una historia, ya sea de usuario o técnica, está terminada cuando se cumple todo esto:

- [ ] Cumple los criterios de aceptación en Gherkin que están en el Issue.
- [ ] El código está en un Pull Request enlazado al Issue (`Closes #N`).
- [ ] Un Code Owner revisó y aprobó el PR.
- [ ] Probamos a mano cada escenario Gherkin y en el PR explicamos cómo.
- [ ] No hay claves, contraseñas ni datos personales en el código ni en el SQL.
- [ ] Si el cambio altera el diseño, los diagramas (`docs/diagramas/`), los requerimientos (`docs/requerimientos/`), los escenarios (`features/`) y `database/galezarzalBD.sql` quedaron actualizados en el mismo PR.
- [ ] El código respeta la estructura de módulos (una carpeta por módulo, en la raíz del repo).
- [ ] La rama quedó fusionada en `main` y el Issue cerrado.
- [ ] La tarjeta está en Done.

## Para el sprint completo

- [ ] El acta de Sprint Planning, con el Sprint Goal y todo el Sprint Backlog, se subió antes de empezar el sprint.
- [ ] Lo que quedó en `main` funciona siguiendo el README.
- [ ] Se hizo la Sprint Review y se subió su acta, comparada con la de Planning.
- [ ] Se hizo la retrospectiva y se subió su acta.
