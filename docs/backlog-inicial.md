# Backlog inicial

Candidatos para arrancar el Product Backlog. Son propuestas nuestras como grupo dueño; las que entren y su prioridad las decide el Product Owner. Estado revisado sobre `main` el 10 de noviembre de 2026.

## Issues que ya existían en el repositorio anterior

Estos Issues salieron de las revisiones del grupo Agora y siguen siendo válidos. Los pasamos a este repo cuando armemos el backlog.

| Issue | Tema | RF o RNF |
|---|---|---|
| #17 | Las notificaciones nunca se generan | RF-17 |
| #18 | Moderar locales: la pestaña "En revisión" siempre sale vacía | RF-21 |
| #23 | El formulario de producto rechaza precios cerrados como 45000 | RF-10, RF-11 |
| #29 | La recuperación de contraseña mostraba el enlace en pantalla por defecto (ya se corrigió, falta confirmar y cerrar) | RF-04, RNF-03 |

## Candidatos nuevos

| # | Tipo | Propuesta | RF o RNF |
|---|---|---|---|
| 1 | HU | Guardar las preferencias de privacidad y de notificaciones. Hoy el formulario dice "guardada" pero no guarda nada, porque no existe tabla para eso. | RF-19 |
| 2 | HU | Guardar la configuración de la plataforma (nombre, modo mantenimiento, registro abierto, aprobación manual). Hoy tampoco guarda. | RF-22 |
| 3 | HU | Favoritos de productos (hoy solo se pueden marcar locales) y búsqueda de productos que oculte los agotados. | RF-13, RF-14 |
| 4 | HU | Mandar por correo al equipo los mensajes de contacto. Hoy se guardan en la base de datos y el administrador los lista. | RF-24 |
| 5 | HT | Que funcione en Linux: la carpeta `Local/` contra los enlaces `/galezarzal/local/...` y los nombres de tabla con mayúscula inicial. El README ya lo menciona como pendiente. | RNF-06 |
| 6 | HT | Configurar el correo para que `mail()` funcione en el despliegue (recuperación de contraseña). | RF-04, RNF-02 |
| 7 | HT | Escribir el Gherkin de lo que ya existe y confirmar cada RF corriendo la aplicación. | RF-01 a RF-24 |
| 8 | HT | Definir cómo probamos cada escenario Gherkin (a mano y documentado al principio, automático después). | DoD |
| 9 | HT | Documentar el despliegue y, cuando lo veamos en clase, montar CI/CD. | RNF-02 |
| 10 | HT | Rutas que no dependan de la carpeta de instalación. El código tiene escrito a mano el prefijo `/galezarzal/` (104 veces en 33 archivos) y `APP_URL` está fijo en `localhost`. Hoy solo funciona si la carpeta se llama `galezarzal`; para desplegarlo en un dominio propio hay que centralizar la ruta base. | RNF-02, RNF-05 |
