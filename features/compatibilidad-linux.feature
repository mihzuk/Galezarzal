# language: es
Característica: Compatibilidad con servidores Linux
  Como desarrollador
  Quiero que la aplicación distinga bien las mayúsculas en carpetas y tablas
  Para poder desplegarla en un hosting Linux, que es el más común

  # Referencia: Issue #16, RNF-11 y RNF-02

  Escenario: Entrar a un local en un servidor Linux
    Dado que la aplicación está instalada en un servidor Linux que distingue mayúsculas de minúsculas
    Cuando abro el listado de locales y entro a la página de un local
    Entonces la página carga sin error 404

  Escenario: Consultas a la base sin lower_case_table_names
    Dado que MySQL está configurado sin lower_case_table_names=1
    Cuando inicio sesión, creo un local y abro el catálogo
    Entonces ninguna pantalla muestra un error de tabla inexistente
