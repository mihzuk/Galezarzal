# language: es
Característica: Autenticación de usuarios
  Como visitante
  Quiero registrarme e iniciar sesión
  Para acceder a las funciones de mi rol

  # Referencia: RF-02 y RF-03. Es un ejemplo de la línea base; lo validamos al planear el Sprint 1.

  Escenario: Registro exitoso
    Dado que soy un visitante sin cuenta
    Cuando completo nombre, apellido, usuario, correo válido y contraseña de al menos 6 caracteres
    Y las dos contraseñas coinciden
    Entonces se crea mi cuenta con rol Comprador
    Y veo el mensaje "¡Cuenta creada con éxito! Ahora puedes iniciar sesión."

  Escenario: Registro con correo ya usado
    Dado que ya existe una cuenta con el correo "ana@correo.com"
    Cuando intento registrarme con ese correo
    Entonces veo el mensaje "El correo o nombre de usuario ya está registrado."
    Y no se crea una cuenta nueva

  Escenario: Inicio de sesión exitoso
    Dado que tengo una cuenta activa
    Cuando ingreso mi correo y mi contraseña correctos
    Entonces accedo a la vitrina con la sesión iniciada
    Y mis permisos corresponden a mi rol

  Escenario: Inicio de sesión con contraseña incorrecta
    Dado que tengo una cuenta activa
    Cuando ingreso mi correo con una contraseña incorrecta
    Entonces veo el mensaje "Correo o contraseña incorrectos."
    Y no se inicia sesión
