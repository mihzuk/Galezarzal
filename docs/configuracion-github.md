# Configuración de GitHub

Lista de lo que hay que dejar listo en el repositorio. Marcamos cada punto cuando esté hecho.

## Etiquetas

```bash
bash scripts/crear-labels.sh mihzuk/Galezarzal
```

- [x] Etiquetas creadas (`epic`, `user-story`, `tech-story`, `RF`, `RNF`, `bug`, `acta`, `cambio-de-diseño`, `requirement-request` y las de prioridad).

## Tag de la línea base

- [x] Tag `v1.0-baseline` creado desde Releases sobre `main`.

## Protección de `main`

En Settings, Rules, Rulesets, nueva regla para la rama `main`:

Ruleset `proteger-main`, activo:

- [x] Exigir Pull Request antes de fusionar, con al menos una aprobación.
- [x] Exigir revisión de los Code Owners, para que solo aprueben los que están en `.github/CODEOWNERS`.
- [x] Bloquear los force push y el borrado de la rama.

## Project (tablero Kanban)

Proyecto "Galezarzal - Tablero Scrum", público.

- [x] Columnas del campo Status: Backlog, Sprint Backlog, In Progress, In Review y Done.
- [x] Campos: Sprint (Iteration, por ahora de 2 semanas; se ajusta cuando se confirme la duración), Story Points (número), Prioridad y Tipo (selección única).
- [x] Workflows: item agregado va a Backlog; item cerrado va a Done; pull request fusionado va a Done.
- [x] Agregar automáticamente al Project los Issues nuevos del repo.
- [x] Vistas: Board para el Kanban, Table para el Product Backlog y Roadmap para los sprints.

## Colaboradores

El repositorio es de una cuenta personal, así que todos los colaboradores entran con permiso de escritura. Solo aprueban los Code Owners.

- [x] Invitaciones enviadas a Galezarzal (Johan-v-p, Josdaver3000, lizarazo2005) y a Agora (divis-smith, Juacoelseco, montanovalentina-cyber, cristianfelipearango-cmyk).
- [x] Los mismos siete con rol Write en el Project.
- [ ] Que cada persona acepte su invitación al repositorio. Faltan `Juacoelseco`, `lizarazo2005` y `montanovalentina-cyber`.
- [x] El profe ve el repositorio y el Project porque son públicos.

## Primer sprint

- [ ] Sprint Planning hecho y su acta subida a `docs/actas/`.
- [ ] Épicas e historias creadas como Issues con las plantillas.
