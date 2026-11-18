# language: es
Característica: Guardar preferencias de usuario
  Como comprador registrado
  Quiero configurar mis preferencias de privacidad y notificaciones
  Para conservar mis ajustes cuando vuelva a ingresar

  # Referencia: Issue #6 y RF-19

  Antecedentes:
    Dado que tengo una cuenta registrada

  Escenario: Guardar preferencias de notificaciones
    Dado que he iniciado sesión
    Cuando desactivo las notificaciones por correo y guardo
    Entonces se muestra un mensaje de confirmación
    Y la preferencia queda guardada en la base de datos

  Escenario: Guardar preferencias de privacidad
    Dado que he iniciado sesión
    Cuando desactivo la opción de mostrar mi nombre completo y guardo
    Entonces se muestra un mensaje de confirmación
    Y la preferencia queda guardada en la base de datos

  Escenario: Conservar preferencias después de cerrar sesión
    Dado que he guardado mis preferencias
    Cuando cierro sesión y vuelvo a ingresar
    Entonces mis preferencias siguen como las dejé

  Escenario: Acceder a preferencias sin iniciar sesión
    Dado que no he iniciado sesión
    Cuando intento entrar a la configuración de preferencias
    Entonces el sistema me redirige al inicio de sesión
