# language: es
Característica: Precio de un producto
  Como vendedor
  Quiero guardar productos con precios cerrados
  Para que el catálogo muestre el precio que escribí

  # Referencia: Issue #32, RF-10 y RF-11

  Escenario: Guardar un precio cerrado
    Dado que soy vendedor con un local activo
    Cuando agrego un producto con precio 45000
    Entonces el producto se guarda y en el catálogo se ve $45.000

  Escenario: Precio no válido
    Dado que soy vendedor con un local activo
    Cuando intento guardar un producto con precio 0
    Entonces no se guarda y veo un mensaje que explica el error