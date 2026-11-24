# Issue #55 - RNF-01
Característica: Pie de página sin enlaces rotos
  Como visitante
  Quiero que todo enlace del pie de página lleve a algún lado
  Para no tocar enlaces que no hacen nada

  Escenario: Galezarzal no tiene redes sociales
    Dado que Galezarzal no tiene cuentas de Facebook, Instagram ni WhatsApp
    Cuando veo el pie de página en cualquier pantalla
    Entonces veo el nombre del sitio y «La moda de Zarzal, ahora digital»
    Y no veo enlaces a Facebook, Instagram ni WhatsApp

  Escenario: Ningún enlace del pie vuelve al inicio de la página
    Dado que estoy al final de una página larga
    Cuando reviso los enlaces del pie de página
    Entonces ninguno apunta a «#»
