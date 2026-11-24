# Prueba con usuarios (#72)

Plan para probar Galezarzal con personas reales **antes** y **después** de los cambios de la épica #49, y medir con datos cuánto mejoró.

- **Antes:** con la versión etiquetada `ux-antes` (el sitio tal como estaba el 2026-11-24, antes de cualquier cambio de interfaz).
- **Después:** con `main` cuando estén fusionadas las historias principales de la épica (#51, #57, #60, #61, #62, #69 y #71).

Las dos rondas usan las mismas tareas, el mismo cuestionario y los mismos datos ([`datos-prueba.sql`](datos-prueba.sql)), para que lo único que cambie sea la interfaz.

## Qué queremos saber

1. ¿Una persona que entra por primera vez entiende qué es Galezarzal?
2. ¿Puede encontrar un producto concreto desde el celular?
3. ¿Puede guardarlo y escribirle al vendedor sin ayuda?
4. ¿Qué tan fácil le parece el sitio? (cuestionario SUS, de 0 a 100)

## Participantes

- **5 personas por ronda** (10 en total, distintas en cada ronda). Con 5 personas se encuentran la mayoría de los problemas de uso importantes; más personas repiten los mismos hallazgos.
- Personas que compran ropa y usan WhatsApp en el celular. Lo ideal: al menos 3 que compren o hayan comprado en la Galería de Zarzal.
- **No** sirven integrantes de los equipos de Galezarzal ni de Ágora, ni estudiantes de Ingeniería de Sistemas que hayan visto el proyecto: ya conocen el sitio.
- Cada prueba dura unos **20 minutos**.

## Qué se necesita

- Un computador con XAMPP y el proyecto (la misma instalación del README).
- El **celular del participante** o uno de prueba, conectado al **mismo wifi** que el computador.
- Un segundo celular o un cronómetro para medir el tiempo.
- La [hoja del participante](hoja-participante.md), impresa o en otra pantalla, una por persona.
- Opcional: grabar **solo la pantalla** del celular (no la cara), si el participante acepta.

## Preparar el sitio (una vez por ronda)

> Esto reemplaza la base `galezarzal` del computador. Si tienes datos que quieras conservar, primero saca una copia desde el Shell de XAMPP: `mysqldump -u root galezarzal > copia-galezarzal.sql`

1. **Poner la versión de la ronda** en `C:\xampp\htdocs\galezarzal`:
   ```
   git fetch --all --tags
   git checkout ux-antes        # ronda «antes»
   git checkout main            # ronda «después» (cuando toque)
   ```
2. **Cargar la base**, siempre con el esquema de `ux-antes` más los datos de prueba, en las dos rondas:
   1. En phpMyAdmin, borrar la base `galezarzal` y crearla de nuevo vacía (`utf8mb4_unicode_ci`).
   2. Importar, en este orden, desde `ux-antes`: `database/galezarzalBD.sql`, `database/configuracion.sql`, `database/mensajeContacto.sql`, `database/preferenciasUsuario.sql` y `database/tokenRecuperacion.sql`.
   3. Abrir `docs/ux/datos-prueba.sql`, cambiar `573000000000` por **tu número de WhatsApp** (57 + 10 dígitos) e importarlo.
   - Si en la ronda «después» el equipo cambió la base, se importa el esquema nuevo y luego `datos-prueba.sql`; si da error, avisar al equipo antes de la prueba.
3. **Abrir el sitio desde el celular:**
   1. En el computador, ejecutar `ipconfig` y anotar la «Dirección IPv4» del wifi (por ejemplo `192.168.1.20`).
   2. En el celular, abrir `http://192.168.1.20/galezarzal/`.
   3. Si no carga, en Windows permitir **Apache HTTP Server** en «redes privadas» (Firewall de Windows → Permitir una aplicación).
4. **Revisión previa (5 minutos):** hacer tú mismo las 3 tareas en el celular. Si algo no funciona por la instalación (no por el diseño), corregirlo antes de que llegue el participante.
5. **Entre un participante y otro:** cerrar la sesión en el sitio, borrar los favoritos y las cuentas creadas (o recargar la base desde el paso 2) y borrar el historial del navegador del celular, para que el siguiente empiece igual.

## Guion del moderador

Lee el guion casi textual: así todos los participantes reciben las mismas instrucciones.

**1. Bienvenida (2 min)**
> Gracias por ayudarnos. Estamos probando una página web, **no a ti**: si algo no te sale, es culpa de la página y eso es justo lo que queremos encontrar. No hay respuestas buenas ni malas.
>
> Te voy a pedir unas tareas cortas en el celular. Mientras las haces, **piensa en voz alta**: dime qué estás buscando, qué esperas que pase y qué te confunde. Yo no te voy a poder ayudar mientras lo intentas, pero al final te explico todo.
>
> No guardamos tu nombre ni tus datos: solo anotamos lo que pasa en la pantalla. ¿Puedo grabar la pantalla del celular, sin tu cara? ¿Tienes alguna pregunta?

**2. Primera impresión (1 min)**
Abre el inicio del sitio en el celular, déjalo ver **5 segundos** y retíralo.
> ¿Qué crees que es esta página? ¿Qué podrías hacer ahí?

Anota la respuesta textual en la hoja. Se considera **acertada** si menciona que es para ver o comprar ropa de locales de Zarzal o de la Galería.

**3. Tareas (12 min)**
Lee cada tarea en voz alta y entrégala escrita. Empieza a medir el tiempo cuando termines de leerla. Si pasan **3 minutos** sin lograrla, o la persona se rinde, para y anótala como no lograda.

| # | Tarea (lo que se lee) | Se logra cuando… |
|---|---|---|
| 1 | «Quieres comprar unos **tenis para ti en talla 40** y no quieres gastar más de **$200.000**. Busca unos en la página y dime cuánto cuestan y en qué local están.» | Dice «Tenis urbanos blancos», $149.900, Calzado El Paso (o abre ese producto). |
| 2 | «Te gustaron y quieres **guardarlos para verlos después**.» | El producto queda en sus favoritos. Si la página le pide crear una cuenta, la crea él mismo; dale un correo inventado si no quiere usar el suyo. |
| 3 | «Quieres preguntarle al vendedor **si los tiene en color negro**. Abre el chat con el vendedor, pero **no envíes** el mensaje.» | Se abre WhatsApp con el chat del vendedor. |

Durante las tareas **no ayudes**. Si pregunta, responde: «¿Qué harías si yo no estuviera?». Solo intervén si está bloqueado más de 3 minutos.

Anota en la hoja, por tarea: si la logró, el tiempo, cuántas veces se equivocó de camino y **frases textuales** («no sé dónde está el menú», «¿esto es un botón?»).

**4. Cuestionario SUS (3 min)**
Entrega el cuestionario de la hoja. Que lo llene solo, sin explicarle las preguntas.

**5. Cierre (2 min)**
> ¿Qué fue lo más difícil? ¿Qué cambiarías primero? ¿Comprarías así en la Galería?

Agradece y, si hubo tareas no logradas, muéstrale cómo se hacían.

## Cómo se calcula el SUS

Cada pregunta se responde de 1 (totalmente en desacuerdo) a 5 (totalmente de acuerdo).

1. Preguntas **impares** (1, 3, 5, 7, 9): puntaje = respuesta − 1.
2. Preguntas **pares** (2, 4, 6, 8, 10): puntaje = 5 − respuesta.
3. Se suman los 10 puntajes y se multiplica por **2,5**. Resultado: de 0 a 100.

Ejemplo: si las respuestas son 4, 2, 4, 1, 4, 2, 5, 2, 4, 1 → (3+3+3+4+3+3+4+3+3+4) × 2,5 = 33 × 2,5 = **82,5**.

El SUS **no es un porcentaje**. Como referencia, el promedio de muchos sitios y aplicaciones evaluados está alrededor de **68**: por encima es mejor que el promedio y por debajo, peor.

## Qué se entrega

- Las hojas llenas (o su transcripción) se resumen en [`resultados.md`](resultados.md). Las hojas en papel no se suben al repositorio.
- Si aparece un problema que ninguna issue cubre, se crea una issue nueva y se enlaza en los resultados.

## Cuidado con los datos personales

- Nada de nombres, teléfonos ni correos de los participantes: «Persona 1», rango de edad y si compra en la Galería.
- Si alguien crea una cuenta con su correo real durante la tarea 2, se borra al terminar (paso 5 de la preparación).
- Si se graba la pantalla, el video se queda con el moderador y no se sube a GitHub.
