# language: es
Característica: Correo de los mensajes de contacto
  Como administrador de Galezarzal
  Quiero que cada mensaje del formulario de contacto me llegue también por correo
  Para responder rápido sin tener que entrar al panel a ver si hay mensajes nuevos

  # Referencia: Issue #35 y RF-24

  Escenario: Un usuario envía un mensaje de contacto
    Dado que el servidor tiene el correo configurado
    Cuando un usuario envía el formulario de Ayuda y Contacto
    Entonces el mensaje se guarda
    Y el equipo recibe un correo con el asunto, el texto y quién lo envió

  Escenario: El servidor no puede enviar el correo
    Dado que el servidor no tiene el correo configurado
    Cuando un usuario envía el formulario de Ayuda y Contacto
    Entonces el mensaje se guarda igual y aparece en el panel de mensajes de contacto
    Y el error del correo queda en el registro de errores
