# Issue #71 - RF-08, RF-09, RNF-01
Característica: Mapa interactivo de la Galería
  Como comprador que conoce la Galería de Zarzal
  Quiero ver un plano con los locales numerados y tocar uno para ver su catálogo
  Para encontrar los locales como los encuentro en persona y saber cuáles están abiertos

  Antecedentes:
    Dado que «el niche store» está activo, su ubicación es «Local 5»
    Y atiende de lunes a sábado de 8:00 a. m. a 6:00 p. m.

  Escenario: Abrir un local desde el mapa
    Cuando abro el mapa y toco el puesto 5
    Entonces veo la tarjeta de «el niche store» con su categoría y el botón «Ver catálogo»
    Y al tocar «Ver catálogo» llego a la página del local

  Escenario: Ver solo los locales abiertos
    Dado que es martes a las 10:00 a. m. en Zarzal
    Cuando activo «Solo abiertos ahora»
    Entonces el puesto 5 se ve en verde y los locales cerrados se ven atenuados

  Escenario: Un local cerrado
    Dado que es domingo
    Cuando abro el mapa
    Entonces el puesto 5 se ve como cerrado y su tarjeta dice «Cerrado»

  Escenario: Puesto sin local en Galezarzal
    Dado que el puesto 12 no tiene ningún local activo
    Cuando toco el puesto 12
    Entonces no se abre ninguna tarjeta y el puesto se ve en gris

  Escenario: Filtrar por categoría
    Cuando elijo la categoría «Calzado» en el mapa
    Entonces solo los puestos de locales de Calzado se ven resaltados

  Escenario: Usar el mapa sin ratón
    Dado que navego con el teclado
    Cuando paso por los puestos con Tab y pulso Enter en el puesto 5
    Entonces se abre la tarjeta de «el niche store», igual que al tocarlo

  Escenario: Local sin número de puesto
    Dado que un local activo tiene como ubicación «Segundo piso»
    Cuando abro el mapa
    Entonces ese local no aparece en el plano
    Y el mapa avisa que hay locales sin ubicar con un enlace a Locales
