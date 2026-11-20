# Acta de Sprint Planning, Sprint 2

| Campo | Detalle |
|---|---|
| Sprint | 2 |
| Fecha y hora de la reunión | 2026-11-19, de 21:00 a 21:45 |
| Duración del sprint | del 2026-11-18 al 2026-12-01 |
| Lugar o medio | meet |
| Scrum Master | Cristian Felipe Arango Gómez |
| Product Owner | Michael Calderon |
| Equipo de desarrollo | Cristian Felipe Arango Gómez, Divis Smith Ramirez, Juan Manuel Cardona |
| Ausentes y motivo | Juan Manuel Cardona (sin motivo justificado) |

## Product Goal

Que los comerciantes de la Galería de Zarzal tengan el marketplace publicado en línea, con todo lo que el sitio ya muestra funcionando de verdad: las preferencias, la configuración, los favoritos de productos, las notificaciones y el contacto.

## Sprint Goal

Dejar el marketplace listo para publicarse: que los locales se puedan moderar y aprobar, que los productos se creen con precios normales en COP, que las notificaciones se generen, que la configuración de la plataforma se aplique y que los mensajes de contacto lleguen por correo. Esto importa porque sin catálogo aprobado y sin un sitio seguro y desplegable (credenciales fuera del código, sesiones seguras, base de datos de producción y compatibilidad con Linux), los comerciantes no pueden usar el sitio en línea.

## Sprint Backlog

| Issue | Tipo | Título | RF o RNF | Story points | Responsable | Gherkin listo |
|---|---|---|---|---|---|---|
| #31 | Bug | Moderar locales: los pendientes no aparecen ni se aprueban | RF-21 | 4 | Divis Smith Ramirez | Sí |
| #32 | Bug | El formulario rechaza precios como 45000 | RF-10, RF-11 | 5 | Divis Smith Ramirez | Sí |
| #18 | HT | Credenciales de la BD fuera del código | RNF-08, RNF-03 | 3 | Divis Smith Ramirez | Sí |
| #33 | Bug | Las notificaciones nunca se generan | RF-17 | 6 | Divis Smith Ramirez | Sí |
| #19 | HT | Sesiones más seguras | RNF-03, RNF-10 | 5 | Divis Smith Ramirez | Sí |
| #41 | HU | Aplicar modo mantenimiento, aprobación manual y nombre del sitio | RF-22 | 7 | Cristian Felipe Arango Gómez | Sí |
| #17 | HT | Base de datos lista para producción | RNF-08, RNF-03 | 6 | Cristian Felipe Arango Gómez | Sí |
| #16 | HT | Que funcione en servidores Linux | RNF-11, RNF-02 | 6 | Cristian Felipe Arango Gómez | Sí |
| #11 | HT | Configurar el correo para recuperar la contraseña | RF-04, RNF-02, RNF-03 | 6 | Cristian Felipe Arango Gómez | Sí |
| #35 | HU | Enviar por correo los mensajes de contacto | RF-24 | 6 | Cristian Felipe Arango Gómez | Sí |
| #43 | HT | Entorno de desarrollo con Docker | RNF-07, RNF-08, RNF-11 | 7 | Juan Manuel Cardona | Sí |
| #9 | HU | Buscar productos y ocultar los agotados | RF-13 | 5 | Juan Manuel Cardona | Sí |
| #10 | HT | Rutas que no dependan de la carpeta de instalación | RNF-02, RNF-05 | 4 | Juan Manuel Cardona | Sí |

Total de story points comprometidos: 70 (Divis 23, Cristian 31, Juan Manuel 16)

Capacidad estimada del equipo: 70 story points

Cómo se calculó la capacidad (quiénes están disponibles y qué se terminó en el sprint anterior): Se basó en la capacidad mostrada por el equipo en el sprint anterior.

### Por qué se priorizó así

- **#31:** bloquea el flujo central del marketplace; sin aprobar locales no hay catálogo.
- **#32:** impide crear productos con precios normales en COP.
- **#18:** es un riesgo de seguridad real y el repositorio es público.
- **#33:** es una función entera que no sirve, aunque no bloquea el uso básico.
- **#19:** mejora importante de seguridad; puede esperar a que lo funcional esté resuelto.
- **#41:** completa la configuración de la plataforma (RF-22), que era parte del objetivo del Sprint 1.
- **#17:** necesaria para desplegar, pero no urgente mientras se trabaje en local.
- **#16:** importa al desplegar (por ejemplo, la sensibilidad a mayúsculas en rutas); hoy el equipo trabaja en XAMPP/Windows.
- **#11:** útil, pero ya existe un flujo con token que sirve en desarrollo.
- **#35:** es un extra; si los mensajes ya se guardan y el administrador los ve, el correo es secundario.

## Daily de este sprint

Horario y asistentes de la Daily: 9:00 p. m., con asistencia de todo el equipo de desarrollo (Cristian Felipe Arango Gómez, Divis Smith Ramirez y Juan Manuel Cardona). Quien no asista queda registrado con falta total.

Todas las historias cumplen el DoR (docs/DoR.md): PARA RELLENAR (sí / no; si alguna no lo cumple, decir cuál y por qué entra igual).

## Criterio de terminado

Aplica docs/DoD.md.

## Dependencias, riesgos e impedimentos

- Responsabilidad horaria: cada integrante cumple los horarios acordados (Daily y entregas del sprint).

## Acuerdos de la reunión

- La Daily es a las 9:00 p. m. y asiste todo el equipo; la inasistencia cuenta como falta total.
- No cometer errores en el trabajo del sprint; si se comete alguno, se reporta ante el profesor.