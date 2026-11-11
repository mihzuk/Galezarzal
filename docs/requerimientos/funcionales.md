# Requerimientos funcionales (línea base)

Vienen del documento funcional de Desarrollo de Software I (metodología ICONIX), sección 2.2 (`docs/legacy/Documento_Funcional_ICONIX.pdf`). Los requerimientos nuevos siguen la numeración desde el RF-25 y entran como Issue con la plantilla.

En la columna Estado anotamos si el requerimiento ya está hecho en `main`: "Sí" cuando la operación principal existe en el código y "Parcial" cuando falta algo, con la explicación.

| ID | Módulo | Descripción | Estado |
|---|---|---|---|
| RF-01 | Vitrina | El sistema debe permitir a cualquier visitante consultar el catálogo de locales y productos sin necesidad de registro. | Sí |
| RF-02 | Autenticación | El sistema debe permitir el registro de nuevos usuarios con nombre de usuario, correo electrónico y contraseña. | Sí |
| RF-03 | Autenticación | El sistema debe autenticar usuarios mediante correo electrónico y contraseña, asignando rol y permisos según el tipo de cuenta (visitante, comprador, vendedor, administrador). | Sí |
| RF-04 | Autenticación | El sistema debe permitir restablecer la contraseña mediante un correo de recuperación enviado al email registrado. | Sí: token de un solo uso que vence a los 60 min; el envío usa `mail()` y requiere correo configurado en el servidor |
| RF-05 | Locales | El sistema debe permitir al vendedor crear un local con nombre, categoría, descripción, número de local en la galería, teléfono/WhatsApp, correo de contacto y horario de atención. | Sí |
| RF-06 | Locales | El sistema debe permitir al vendedor modificar la información de su local en cualquier momento. | Sí |
| RF-07 | Locales | El sistema debe permitir al administrador o dueño desactivar (eliminar lógicamente) un local de la plataforma. | Sí |
| RF-08 | Locales | El sistema debe permitir buscar locales por nombre, categoría o palabra clave. | Sí |
| RF-09 | Locales | El sistema debe permitir ver el detalle de un local con su información completa, horarios y catálogo de productos. | Sí |
| RF-10 | Catálogo | El sistema debe permitir al vendedor agregar productos a su catálogo con nombre, categoría, precio, tallas disponibles, descripción, foto y sección. | Sí |
| RF-11 | Catálogo | El sistema debe permitir al vendedor modificar los datos de un producto existente. | Sí |
| RF-12 | Catálogo | El sistema debe permitir al vendedor eliminar (desactivar) un producto de su catálogo. | Sí |
| RF-13 | Catálogo | El sistema debe permitir al vendedor cambiar el estado de un producto entre "disponible" y "agotado". Cuando un producto está agotado, debe ocultarse automáticamente de los resultados de búsqueda activos. | Parcial: cambia el estado y oculta agotados en el local; no hay búsqueda de productos |
| RF-14 | Favoritos | El sistema debe permitir al usuario registrado agregar locales o productos a su lista de favoritos. | Parcial: solo se pueden marcar locales; productos no |
| RF-15 | Favoritos | El sistema debe permitir al usuario registrado eliminar elementos de su lista de favoritos. | Sí |
| RF-16 | Favoritos | El sistema debe permitir al usuario registrado consultar su lista de favoritos. | Sí |
| RF-17 | Notificaciones | El sistema debe enviar notificaciones a los usuarios sobre nuevos productos en locales favoritos, respuestas a consultas y novedades de la plataforma. | Parcial: se pueden consultar, pero ningún proceso genera notificaciones |
| RF-18 | Notificaciones | El sistema debe permitir al usuario registrado consultar, marcar como leída y eliminar notificaciones. | Sí |
| RF-19 | Configuración | El sistema debe permitir al usuario registrado modificar su perfil, cambiar su contraseña, configurar preferencias de notificaciones y ajustar opciones de privacidad. | Parcial: perfil y contraseña sí; privacidad y preferencias de notificación solo muestran un mensaje, no guardan |
| RF-20 | Administración | El sistema debe permitir al administrador gestionar usuarios (activar, suspender, eliminar). | Sí |
| RF-21 | Administración | El sistema debe permitir al administrador moderar locales y productos (aprobar, reportar, desactivar). | Sí |
| RF-22 | Administración | El sistema debe permitir al administrador configurar parámetros generales de la plataforma como registro abierto, modo mantenimiento y aprobación manual de locales o productos. | Parcial: la pantalla existe pero no guarda la configuración |
| RF-23 | Ayudas | El sistema debe ofrecer una sección de ayuda con preguntas frecuentes y guías de uso diferenciadas por rol. | Sí |
| RF-24 | Ayudas | El sistema debe permitir a cualquier usuario enviar un formulario de contacto/PQR que genere un correo al equipo de Galezarzal. | Parcial: el mensaje se guarda en la BD y el administrador lo lista (`admin/mensajesContacto.php`); no envía correo |

El estado lo sacamos leyendo el código (buscamos las consultas y los formularios de cada requerimiento), no corriendo la aplicación. Cuando escribamos el Gherkin de cada uno lo vamos a confirmar probando.
