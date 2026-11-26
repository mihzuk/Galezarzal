# Issue #9 - RF-13
Característica: Búsqueda de productos
  Como comprador
  Quiero buscar productos por nombre y no ver los agotados
  Para encontrar productos que puedo comprar

  Escenario: Buscar un producto disponible
    Dado que existe un producto disponible llamado "Chaqueta de cuero"
    Cuando busco "chaqueta"
    Entonces veo "Chaqueta de cuero" en los resultados

  Escenario: Un producto agotado no aparece
    Dado que el producto "Chaqueta de cuero" está agotado
    Cuando busco "chaqueta"
    Entonces no veo "Chaqueta de cuero" en los resultados

  Escenario: Búsqueda sin resultados
    Dado que no hay productos disponibles que coincidan con "sombrero"
    Cuando busco "sombrero"
    Entonces veo el mensaje "No se encontraron productos"
