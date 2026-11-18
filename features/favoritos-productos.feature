# language: es
Característica: Favoritos de productos
  Como comprador registrado
  Quiero marcar productos como favoritos
  Para encontrarlos fácilmente después, sin tener que buscar el local de nuevo

  # Referencia: Issue #8 y RF-14

  Escenario: Marcar un producto como favorito
    Dado que tengo la sesión iniciada y estoy viendo un producto
    Cuando pulso el botón de favorito
    Entonces el producto queda en mi lista de favoritos
    Y la lista me lo muestra junto a mis locales favoritos

  Escenario: Visitante sin sesión
    Dado que soy un visitante sin sesión
    Cuando pulso el botón de favorito de un producto
    Entonces se me pide iniciar sesión
    Y el producto no se guarda