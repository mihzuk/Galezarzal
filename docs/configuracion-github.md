# Configuración de GitHub para el proceso (checklist)

Marca cada punto cuando esté listo. El repositorio ya tiene `CODEOWNERS` y una regla de protección para `main` (ruleset `proteger-main`); aquí solo se verifica y se completa lo que falta.

## 1. Etiquetas
```bash
./scripts/crear-labels.sh mihzuk/Galezarzal     # requiere GitHub CLI autenticado
```
- [ ] Etiquetas creadas (`epic`, `user-story`, `tech-story`, `RF`, `RNF`, `bug`, `acta`, `cambio-de-diseño`, `requirement-request`, prioridades).

## 2. Protección de `main`
Settings → Rules → Rulesets → `proteger-main`. Verifica que tenga:
- [ ] Require a pull request before merging, con al menos 1 aprobación.
- [ ] **Require review from Code Owners** (para que solo aprueben los usuarios de `.github/CODEOWNERS`).
- [ ] Block force pushes.

## 3. Project (tablero Kanban)
El README ya habla de un Project con Backlog / In progress. Compruébalo y complétalo:
- [ ] Columnas (campo *Status*): Backlog, Sprint Backlog, In Progress, In Review, Done.
- [ ] Campos: **Sprint** (Iteration, 1–2 semanas), **Story Points** (Number), **Prioridad** (Single select), **Tipo** (Single select).
- [ ] Workflows: *Item added to project* → Backlog; *Item closed* → Done; *Pull request merged* → Done.
- [ ] Auto-add: añadir al Project los Issues nuevos del repo.
- [ ] Vistas: Board (Kanban), Table (Product Backlog), Roadmap (por Sprint).

## 4. Línea base
- [ ] Después de fusionar el PR del proceso: `git tag v1.0-baseline && git push origin v1.0-baseline`.

## 5. Primer Sprint
- [ ] Sprint Planning con acta en `docs/actas/`.
- [ ] Épicas e historias creadas como Issues con las plantillas.
