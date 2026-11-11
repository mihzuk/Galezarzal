# Backlog inicial (candidatos)

Punto de partida para el Product Backlog. Son **propuestas**: el Product Owner decide qué entra y con qué prioridad. Estado revisado sobre `main` el 2026-11-10.

## Issues que ya existen en el repositorio
Ya cumplen la función de backlog; conviene etiquetarlos (`user-story`, `tech-story`, `bug`), estimarlos y cargarlos al Project.

| Issue | Tema | Relacionado |
|---|---|---|
| #17 | Las notificaciones nunca se generan | RF-17 |
| #18 | Moderar locales: la pestaña "En revisión" siempre está vacía (hay un PR abierto, #33) | RF-21 |
| #23 | El formulario de producto rechaza precios cerrados como 45000 (hay un PR abierto, #31) | RF-10, RF-11 |
| #29 | Seguridad: la recuperación de contraseña mostraba el enlace en pantalla por defecto. El PR #30 ya se fusionó: verificar y cerrar | RF-04, RNF-03 |

## Candidatos nuevos

| # | Tipo | Propuesta | Relacionado |
|---|---|---|---|
| 1 | HU | **Guardar preferencias de privacidad y de notificaciones.** Hoy el formulario responde "guardada" sin persistir (no existe tabla de preferencias). | RF-19 |
| 2 | HU | **Persistir la configuración de la plataforma** (nombre, modo mantenimiento, registro abierto, aprobación manual). Hoy no guarda. | RF-22 |
| 3 | HU | **Favoritos de productos** (hoy solo locales) y **búsqueda de productos** que oculte agotados. | RF-13, RF-14 |
| 4 | HU | **Enviar por correo los mensajes de contacto al equipo** (hoy se guardan en la BD y los lista el administrador). | RF-24 |
| 5 | HT | **Compatibilidad con Linux:** carpeta `Local/` vs enlaces `/galezarzal/local/...` y nombres de tabla con mayúscula inicial. Ya está descrito en el README como pendiente. | RNF-06 |
| 6 | HT | **Configuración de correo** para que `mail()` funcione en el despliegue (recuperación de contraseña). | RF-04, RNF-02 |
| 7 | HT | **Escribir el Gherkin de la línea base** y confirmar cada RF ejecutando la aplicación. | RF-01…RF-24 |
| 8 | HT | **Pruebas:** definir cómo se prueba cada escenario Gherkin (manual documentada al inicio, automatizada después). | DoD |
| 9 | HT | **Despliegue y DevOps:** documentar el despliegue y, cuando se vea en clase, CI/CD. | RNF-02 |
| 10 | HT | **Rutas independientes de la carpeta de instalación.** El código escribe a mano el prefijo `/galezarzal/` (104 apariciones en 33 archivos) y `APP_URL` está fijo a `localhost`. Hoy la aplicación solo funciona si está en una carpeta llamada `galezarzal`; para desplegarla en un dominio propio hay que centralizar la ruta base. | RNF-02, RNF-05 |
