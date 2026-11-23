# language: es
Característica: Configuración de la plataforma
  Como administrador
  Quiero que la configuración de la plataforma (nombre, modo mantenimiento, registro abierto y aprobación manual) se guarde
  Para controlar cómo funciona el sitio sin tocar el código

  # Referencia: Issues #7 y #41, RF-22

  Escenario: Guardar la configuración
    Dado que soy administrador y estoy en la pantalla de configuración de la plataforma
    Cuando cambio un parámetro y pulso guardar
    Entonces veo un mensaje de confirmación
    Y al volver a abrir la pantalla el parámetro conserva el valor que guardé

  Escenario: Registro abierto desactivado
    Dado que el administrador desactivó el registro abierto
    Cuando un visitante intenta crear una cuenta
    Entonces no se crea la cuenta
    Y el visitante ve un mensaje que explica que el registro está cerrado

  # Issue #41: la configuración guardada se aplica en el resto del sitio

  Escenario: Modo mantenimiento activado
    Dado que el administrador activó el modo mantenimiento
    Cuando un visitante o un usuario que no es administrador abre cualquier página del sitio
    Entonces ve un aviso de que el sitio está en mantenimiento
    Y no puede usar el resto del sitio

  Escenario: El administrador entra durante el mantenimiento
    Dado que el modo mantenimiento está activado
    Cuando un administrador inicia sesión
    Entonces puede usar el panel de administración para desactivarlo

  Escenario: Aprobación manual desactivada
    Dado que el administrador desactivó la aprobación manual
    Cuando un vendedor crea un local
    Entonces el local queda activo sin pasar por revisión

  Escenario: Aprobación manual activada
    Dado que el administrador activó la aprobación manual
    Cuando un vendedor crea un local
    Entonces el local queda pendiente hasta que un administrador lo apruebe

  Escenario: Cambiar el nombre del sitio
    Dado que el administrador cambió el nombre del sitio
    Cuando cualquier persona abre una página del sitio
    Entonces ve el nombre nuevo en el título y en el encabezado
