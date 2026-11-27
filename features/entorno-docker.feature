# language: es
Característica: Entorno de desarrollo con Docker
  Como desarrollador
  Quiero ejecutar Galezarzal mediante Docker Compose
  Para tener un entorno de desarrollo uniforme y reproducible en diferentes equipos

  # Referencia: Issue #43, RNF-07, RNF-08 y RNF-11

  Escenario: Inicialización automática del entorno Docker
    Dado que tengo Docker instalado y he clonado el repositorio
    Y he configurado el archivo .env a partir de .env.example
    Cuando ejecuto docker compose up
    Entonces se inician los servicios web y db
    Y la base de datos se inicializa desde database/galezarzalBD.sql
    Y puedo consultar el catálogo con los datos de ejemplo

  Escenario: Persistencia de las cuentas de usuario
    Dado que la aplicación está funcionando mediante Docker
    Y he registrado una nueva cuenta de usuario
    Cuando detengo y vuelvo a iniciar los contenedores
    Entonces la cuenta permanece registrada en la base de datos
    Y puedo iniciar sesión con la misma cuenta

  Escenario: Protección de las credenciales de la base de datos
    Dado que el entorno Docker utiliza variables de entorno
    Cuando reviso los archivos versionados en Git
    Entonces encuentro el archivo .env.example con valores de ejemplo
    Y el archivo .env con las credenciales locales no está versionado
    Y las credenciales de conexión se obtienen de las variables de entorno

  Escenario: Compatibilidad con la instalación tradicional de XAMPP
    Dado que tengo Galezarzal instalado mediante XAMPP
    Cuando inicio Apache y MySQL desde XAMPP
    Y accedo a la aplicación desde el navegador
    Entonces la aplicación funciona sin depender de Docker
    Y puedo utilizar sus funcionalidades habituales
