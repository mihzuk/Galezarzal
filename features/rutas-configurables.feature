# Issue #10 - Rutas independientes de la carpeta de instalación
# Épica #5
# Requerimientos: RNF-02 y RNF-05

Característica: Rutas independientes de la carpeta de instalación
  Como desarrollador
  Quiero definir las rutas y la dirección base en un solo lugar
  Para desplegar la aplicación sin modificar los enlaces de cada módulo

  Escenario: Instalada en una carpeta con otro nombre
    Dado que la aplicación está instalada en una carpeta que no se llama "galezarzal"
    Cuando abro la página principal y navego al catálogo, al login y a un local
    Entonces los enlaces funcionan y ninguna pantalla da error 404

  Escenario: La dirección base se cambia en un solo lugar
    Dado que la dirección base está definida en un único archivo de configuración
    Cuando cambio ese valor
    Entonces todos los enlaces generados usan la nueva dirección
