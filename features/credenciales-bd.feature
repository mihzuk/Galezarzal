# language: es
Característica: Credenciales de la base de datos por configuración local
  Como desarrollador
  Necesito que el servidor, el usuario, la contraseña y el nombre de la base se definan fuera de includes/config.php
  Para no subir claves a Git y no usar root sin contraseña en producción

  # Referencia: Issue #18, RNF-08 y RNF-03

  Escenario: Usar otras credenciales en el servidor
    Dado que existe includes/config.local.php, ignorado por Git, con otro usuario y contraseña
    Cuando la aplicación se conecta a la base de datos
    Entonces usa esos valores y no los de includes/config.php

  Escenario: Las credenciales son incorrectas
    Dado que las credenciales configuradas no permiten conectar
    Cuando abro cualquier página
    Entonces veo el mensaje genérico de error
    Y el detalle técnico queda solo en el registro de errores