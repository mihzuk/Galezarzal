# language: es
Característica: Configuración de la plataforma
  Como administrador
  Quiero que la configuración de la plataforma (nombre, modo mantenimiento, registro abierto y aprobación manual) se guarde
  Para controlar cómo funciona el sitio sin tocar el código

  # Referencia: Issue #7 y RF-22

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