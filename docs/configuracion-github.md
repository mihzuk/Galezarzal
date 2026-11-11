# Configuración de GitHub

Lista de lo que hay que dejar listo en el repositorio. Marcamos cada punto cuando esté hecho.

## Etiquetas

```bash
bash scripts/crear-labels.sh mihzuk/Galezarzal
```

- [x] Etiquetas creadas (`epic`, `user-story`, `tech-story`, `RF`, `RNF`, `bug`, `acta`, `cambio-de-diseño`, `requirement-request` y las de prioridad).

## Tag de la línea base

- [ ] Crear el tag `v1.0-baseline` sobre `main` (en GitHub: Releases, Draft a new release, escribir el tag).

## Protección de `main`

En Settings, Rules, Rulesets, nueva regla para la rama `main`:

- [ ] Exigir Pull Request antes de fusionar, con al menos una aprobación.
- [ ] Exigir revisión de los Code Owners, para que solo aprueben los que están en `.github/CODEOWNERS`.
- [ ] Bloquear los force push.

## Project (tablero Kanban)

- [ ] Columnas del campo Status: Backlog, Sprint Backlog, In Progress, In Review y Done.
- [ ] Campos: Sprint (Iteration, de 1 o 2 semanas), Story Points (número), Prioridad y Tipo (selección única).
- [ ] Workflows: item agregado va a Backlog; item cerrado va a Done; pull request fusionado va a Done.
- [ ] Agregar automáticamente al Project los Issues nuevos del repo.
- [ ] Vistas: Board para el Kanban, Table para el Product Backlog y Roadmap para los sprints.

## Colaboradores

- [ ] Grupo Galezarzal con rol Admin o Maintain.
- [ ] Grupo Agora con rol Write (pueden crear ramas y PRs, pero solo aprueban los Code Owners), o trabajando con fork y Pull Request.
- [ ] El profe con acceso de lectura.

## Primer sprint

- [ ] Sprint Planning hecho y su acta subida a `docs/actas/`.
- [ ] Épicas e historias creadas como Issues con las plantillas.
