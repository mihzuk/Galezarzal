-- ============================================================
--  GALEZARZAL — Datos para la prueba con usuarios (#72)
--
--  Se importan DESPUÉS de database/galezarzalBD.sql, en la misma base.
--  Se usan los mismos datos en la prueba «antes» y en la «después»,
--  para que la única diferencia sea la interfaz.
--
--  Todo es inventado: locales, personas y teléfonos.
--  Las cuentas de los vendedores no sirven para iniciar sesión.
--
--  ANTES DE IMPORTAR: cambia 573000000000 por el número de WhatsApp
--  del moderador de la prueba (57 + 10 dígitos, sin + ni espacios),
--  para que el chat que abra el participante sea con el moderador.
-- ============================================================

SET @wa = '573000000000';

-- El local que ya trae la base: solo se le da una descripción legible.
UPDATE `local` SET `describLocal` = 'Tenis y ropa deportiva de marca para hombre y mujer.' WHERE `idLocal` = 1;

-- ---------- Usuarios dueños de los locales (rol Vendedor) ----------
INSERT INTO `usuario` (`iduser`, `username`, `email`, `passwordHash`, `primerNombre`, `primerApellido`, `celular`, `idTipDocu`, `idRol`, `estado`) VALUES
(100, 'prueba_vend1', 'vendedor1@prueba.invalid', 'HASH_DE_EJEMPLO_NO_VALIDO', 'Rosa',    'Prueba', '3000000101', 1, 2, 'activo'),
(101, 'prueba_vend2', 'vendedor2@prueba.invalid', 'HASH_DE_EJEMPLO_NO_VALIDO', 'Andrés',  'Prueba', '3000000102', 1, 2, 'activo'),
(102, 'prueba_vend3', 'vendedor3@prueba.invalid', 'HASH_DE_EJEMPLO_NO_VALIDO', 'Lucía',   'Prueba', '3000000103', 1, 2, 'activo'),
(103, 'prueba_vend4', 'vendedor4@prueba.invalid', 'HASH_DE_EJEMPLO_NO_VALIDO', 'Jairo',   'Prueba', '3000000104', 1, 2, 'activo'),
(104, 'prueba_vend5', 'vendedor5@prueba.invalid', 'HASH_DE_EJEMPLO_NO_VALIDO', 'Marta',   'Prueba', '3000000105', 1, 2, 'activo'),
(105, 'prueba_vend6', 'vendedor6@prueba.invalid', 'HASH_DE_EJEMPLO_NO_VALIDO', 'Esteban', 'Prueba', '3000000106', 1, 2, 'activo');

INSERT INTO `vendedor` (`idVendedor`, `iduser`, `nomVendedor`, `estado`) VALUES
(100, 100, 'Rosa Prueba',    'activo'),
(101, 101, 'Andrés Prueba',  'activo'),
(102, 102, 'Lucía Prueba',   'activo'),
(103, 103, 'Jairo Prueba',   'activo'),
(104, 104, 'Marta Prueba',   'activo'),
(105, 105, 'Esteban Prueba', 'activo');

-- ---------- Horarios ----------
INSERT INTO `horario` (`idHorario`, `horarioApertura`, `horarioCierre`, `descansoOpc`, `diasDisponibles`) VALUES
(100, '08:00:00', '18:00:00', '', 'LUN,MAR,MIE,JUE,VIE,SAB'),
(101, '09:00:00', '19:00:00', '', 'LUN,MAR,MIE,JUE,VIE,SAB,DOM'),
(102, '08:30:00', '17:30:00', '', 'LUN,MAR,MIE,JUE,VIE'),
(103, '10:00:00', '20:00:00', '', 'MAR,MIE,JUE,VIE,SAB,DOM'),
(104, '08:00:00', '12:00:00', '', 'SAB,DOM'),
(105, '09:00:00', '18:00:00', '', 'LUN,MAR,MIE,JUE,VIE,SAB');

-- ---------- Locales (uno por categoría, todos activos) ----------
INSERT INTO `local` (`idLocal`, `nomLocal`, `describLocal`, `ubiLocal`, `urlWhatsapp`, `estado`, `fechaRegistro`, `idHorario`, `idVendedor`) VALUES
(100, 'Boutique Rosa',      'Blusas, vestidos y jeans para mujer. Tallas S a XXL.',                  'Local 3',  @wa, 'activo', '2026-11-01 09:00:00', 100, 100),
(101, 'Estilo Hombre',      'Camisas, jeans y chaquetas para hombre, casual y formal.',              'Local 8',  @wa, 'activo', '2026-11-02 09:00:00', 101, 101),
(102, 'Pequeños Pasos',     'Ropa para niños y niñas de 0 a 12 años, uniformes y conjuntos.',        'Local 11', @wa, 'activo', '2026-11-03 09:00:00', 102, 102),
(103, 'Accesorios Lulú',    'Bolsos, cinturones, aretes y bisutería.',                                'Local 14', @wa, 'activo', '2026-11-04 09:00:00', 103, 103),
(104, 'Calzado El Paso',    'Tenis, zapatos y sandalias para toda la familia.',                       'Local 17', @wa, 'activo', '2026-11-05 09:00:00', 104, 104),
(105, 'Zarzal Fit',         'Licras, camisetas y pantalonetas para hacer deporte.',                   'Local 20', @wa, 'activo', '2026-11-06 09:00:00', 105, 105);

-- El local que ya trae la base también abre el chat con el moderador.
UPDATE `local` SET `urlWhatsapp` = @wa WHERE `idLocal` = 1;

-- ---------- Productos (sin fotos: igual en las dos pruebas) ----------
-- Para la tarea 1 hay una sola respuesta correcta: «Tenis urbanos blancos»
-- (talla 40, $149.900, Calzado El Paso). Los demás tenis son distractores:
-- uno vale más de $200.000, otro no tiene talla 40 y otro está agotado.
INSERT INTO `producto` (`idProducto`, `nomProducto`, `describProducto`, `fotosUrl`, `idcategoria`, `idLocal`, `tipoProducto`, `talla`, `estado`, `precio`, `seccion`) VALUES
(100, 'Blusa de lino blanca',        'Blusa fresca de manga corta.',                       NULL, 1, 100, NULL, 'S, M, L',            'disponible',  59900.00, 'Nuevos'),
(101, 'Vestido floral largo',        'Vestido largo con estampado de flores.',             NULL, 1, 100, NULL, 'S, M, L, XL',        'disponible',  89900.00, 'Destacados'),
(102, 'Jean tiro alto',              'Jean azul clásico de tiro alto.',                    NULL, 1, 100, NULL, '6, 8, 10, 12, 14',   'disponible',  79900.00, 'Destacados'),
(103, 'Camisa de cuadros',           'Camisa manga larga de algodón.',                     NULL, 2, 101, NULL, 'M, L, XL',           'disponible',  69900.00, 'Nuevos'),
(104, 'Jean slim oscuro',            'Jean de corte slim, azul oscuro.',                   NULL, 2, 101, NULL, '30, 32, 34, 36',     'disponible',  99900.00, 'Destacados'),
(105, 'Chaqueta de jean',            'Chaqueta de jean clásica.',                          NULL, 2, 101, NULL, 'M, L',               'disponible', 139900.00, 'Destacados'),
(106, 'Conjunto deportivo niño',     'Sudadera y camiseta para niño.',                     NULL, 3, 102, NULL, '4, 6, 8, 10',        'disponible',  65000.00, 'Nuevos'),
(107, 'Vestido de niña con moño',    'Vestido para ocasiones especiales.',                 NULL, 3, 102, NULL, '2, 4, 6',            'disponible',  72000.00, 'Destacados'),
(108, 'Tenis de lona niño',          'Tenis de lona con velcro.',                          NULL, 5, 102, NULL, '28, 30, 32, 34',     'disponible',  55000.00, 'Remates'),
(109, 'Bolso de cuero café',         'Bolso mediano de cuero sintético.',                  NULL, 4, 103, NULL, 'Única',              'disponible',  85000.00, 'Destacados'),
(110, 'Cinturón de cuero negro',     'Cinturón con hebilla plateada.',                     NULL, 4, 103, NULL, 'Única',              'disponible',  35000.00, 'Nuevos'),
(111, 'Aretes dorados de argolla',   'Aretes de bisutería fina.',                          NULL, 4, 103, NULL, 'Única',              'disponible',  18000.00, 'Remates'),
(112, 'Tenis urbanos blancos',       'Tenis blancos de cuero sintético, suela de goma.',   NULL, 5, 104, NULL, '38, 39, 40, 41, 42', 'disponible', 149900.00, 'Destacados'),
(113, 'Tenis running negros',        'Tenis livianos para correr.',                        NULL, 5, 104, NULL, '39, 40, 41, 42, 43', 'disponible', 219900.00, 'Nuevos'),
(114, 'Tenis de lona azules',        'Tenis de lona clásicos.',                            NULL, 5, 104, NULL, '35, 36, 37, 38',     'disponible',  89900.00, 'Remates'),
(115, 'Tenis plataforma beige',      'Tenis con suela alta.',                              NULL, 5, 104, NULL, '36, 37, 38, 39, 40', 'agotado',   129900.00, 'Destacados'),
(116, 'Sandalias de cuero',          'Sandalias planas para mujer.',                       NULL, 5, 104, NULL, '35, 36, 37, 38, 39', 'disponible',  69900.00, 'Nuevos'),
(117, 'Zapatos formales negros',     'Zapatos de cuero para hombre.',                      NULL, 5, 104, NULL, '39, 40, 41, 42',     'disponible', 189900.00, 'Destacados'),
(118, 'Licra deportiva mujer',       'Licra larga de compresión.',                         NULL, 6, 105, NULL, 'S, M, L',            'disponible',  49900.00, 'Destacados'),
(119, 'Camiseta dry-fit',            'Camiseta deportiva que no retiene el sudor.',        NULL, 6, 105, NULL, 'S, M, L, XL',        'disponible',  39900.00, 'Nuevos'),
(120, 'Pantaloneta de fútbol',       'Pantaloneta liviana.',                               NULL, 6, 105, NULL, 'M, L, XL',           'disponible',  32000.00, 'Remates'),
(121, 'Tenis de entrenamiento',      'Tenis para gimnasio.',                               NULL, 5, 105, NULL, '41, 42, 43',         'disponible', 179900.00, 'Destacados');
