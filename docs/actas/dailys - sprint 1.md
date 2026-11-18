# Acta de Daily 1, Sprint 1

| | |
|---|---|
| Ceremonia | Daily |
| Fecha y hora | 2026-11-15, de 10:15 a 10:30 |
| Lugar o medio | Meet |
| Scrum Master | cristian felipe arango gomez |
| Product Owner | michael calderon |
| Asistentes | juan manuel cardona, cristian felipe arango gomez, valentina montaño |
| Ausentes y motivo | divis smith ramirez: problemas de conectividad debido a problemas eléctricos |

## Temas que vimos
Cada integrante contó qué hizo, qué va a hacer y qué lo bloquea.

- cristian felipe arango gomez: hizo el escenario 1 del #7. Creó la tabla `configuracion` en `database/galezarzalBD.sql` y las funciones de lectura y guardado en `includes/configuracion.php`. Hizo que `admin/configurarPlataforma.php` guarde nombre, modo mantenimiento, registro abierto y aprobación manual, muestre el mensaje de confirmación y conserve los valores al reabrir. Actualizó el diagrama ER, el RF-22 y el escenario en `features/`. Abrió el PR #14 y espera la revisión. No tiene bloqueos.

- divis smith ramirez (ausente, informó su avance): agregó el bloqueo del registro cuando está cerrado en `usuario/ingresarCuenta.php` y `usuario/crearUsuario.php`, y el script `database/configuracion.sql`. Lo probó a mano con el registro abierto y cerrado.

- valentina montaño: empezó a trabajar en el issue #8. Tenía dudas sobre cómo plantear el issue y sobre la creación de la rama, que se resolvieron en la daily.

- juan manuel cardona: empezó con el issue que se le asignó, el #6 (guardar las preferencias de privacidad y de notificaciones). Creó la rama y clonó el repositorio.

## Issues revisados

- #6, Guardar las preferencias de privacidad y de notificaciones, en trabajo (juan manuel cardona: rama creada y repositorio clonado)

- #7, Guardar la configuración de la plataforma, In Review (PR #14)

- #8, Marcar productos como favoritos, en trabajo (valentina montaño)

## Acuerdos y decisiones

1. Cada integrante trabaja en su propia rama, creada a partir de `main`, y no se trabaja directamente en `main`.
2. Antes de abrir un Pull Request se avisa al equipo y se espera su visto bueno.
3. Los commits y los PR enlazan su issue (`Refs #N`, y `Closes #N` solo cuando el issue queda completo).
4. Quien tenga dudas con ramas o con Git las plantea en la daily o al Scrum Master, sin esperar al siguiente día.

## Impedimentos o riesgos

-

## Qué sigue

| Acción | Responsable | Fecha límite |
|---|---|---|
| Esperar la revisión del PR #14 y corregir lo que pidan | cristian felipe arango gomez | 2026-11-17 |
| Subir su parte del #7 (bloqueo del registro) en una rama y un PR, con `Closes #7` | divis smith ramirez | 2026-11-17 |
| Avanzar en el issue #8 sobre su rama | valentina montaño | 2026-11-17 |
| Avanzar en el issue #6 (preferencias de privacidad y de notificaciones) sobre su rama | juan manuel cardona | 2026-11-17 |

## Retrospectiva (solo si es la retro)

no aplica
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

# Acta de Daily 2, Sprint 1

| | |
|---|---|
| Ceremonia | Daily |
| Fecha y hora | 2026-11-16, de 20:00 a 20:15 |
| Lugar o medio | Meet |
| Scrum Master | Cristian Felipe Arango Gómez |
| Product Owner | Michael Calderón |
| Asistentes | Divis Smith Ramírez Barrientos, Cristian Felipe Arango Gómez |
| Ausentes y motivo | Valentina Montaño: ausente por causas laborales. Juan Manuel Cardona: tiene cruce de horario con un turno laboral en la noche y debe desplazarse fuera del municipio, por lo cual no logró conectarse a las 20:00. |

## Temas que vimos

Se revisó el trabajo realizado el día de hoy por Divis Smith Ramírez. Se probaron posibles errores del Pull Request #14 y del issue #7.

## Issues revisados

- #7, Guardar la configuración de la plataforma, en espera de revisión (PR #14)

## Acuerdos y decisiones

1. No se tomaron acuerdos ni decisiones en esta reunión.

## Impedimentos o riesgos

- No se presentaron impedimentos ni riesgos.

## Qué sigue

| Acción | Responsable | Fecha límite |
|---|---|---|
| Esperar la corrección o la aprobación del Pull Request #14 | Divis Smith Ramírez y Cristian Felipe Arango Gómez | 2026-11-16 |

## Retrospectiva (solo si es la retro)

No aplica.