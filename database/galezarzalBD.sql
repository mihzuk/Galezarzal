-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-06-2026 a las 13:46:00
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `galezarzal`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `idcategoria` int(11) NOT NULL,
  `nomCategoria` varchar(60) NOT NULL,
  `describCategoria` varchar(200) DEFAULT NULL,
  `infoCategoria` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`idcategoria`, `nomCategoria`, `describCategoria`, `infoCategoria`) VALUES
(1, 'Ropa Mujer', 'Prendas de vestir para mujer', 'Blusas, vestidos, pantalones, faldas'),
(2, 'Ropa Hombre', 'Prendas de vestir para hombre', 'Camisas, pantalones, chaquetas'),
(3, 'Ropa Niños', 'Prendas de vestir para niños y niñas', 'Conjuntos, uniformes, ropa casual'),
(4, 'Accesorios', 'Complementos de moda', 'Bolsos, cinturones, joyería, bisutería'),
(5, 'Calzado', 'Zapatos y sandalias para todas las edades', 'Zapatos, botas, sandalias, tenis'),
(6, 'Ropa Deportiva', 'Indumentaria para actividad física y deporte', 'Licras, pantalonetas, camisetas deportivas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ciudad`
--

CREATE TABLE `ciudad` (
  `idciud` int(11) NOT NULL,
  `nomCiudad` varchar(60) NOT NULL,
  `describCiu` varchar(150) DEFAULT NULL,
  `idDepart` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ciudad`
--

INSERT INTO `ciudad` (`idciud`, `nomCiudad`, `describCiu`, `idDepart`) VALUES
(1, 'Zarzal', 'Municipio del norte del Valle del Cauca', 1),
(2, 'Cali', 'Capital del Valle del Cauca', 1),
(3, 'Tuluá', 'Ciudad del centro del Valle del Cauca', 1),
(4, 'Cartago', 'Ciudad del norte del Valle del Cauca', 1),
(5, 'Buenaventura', 'Puerto principal del Pacífico colombiano', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departamento`
--

CREATE TABLE `departamento` (
  `idDepart` int(11) NOT NULL,
  `nomDepart` varchar(60) NOT NULL,
  `describDepart` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `departamento`
--

INSERT INTO `departamento` (`idDepart`, `nomDepart`, `describDepart`) VALUES
(1, 'Valle del Cauca', 'Departamento del suroccidente colombiano'),
(2, 'Antioquia', 'Departamento del noroccidente colombiano'),
(3, 'Cundinamarca', 'Departamento del centro de Colombia'),
(4, 'Risaralda', 'Departamento del eje cafetero'),
(5, 'Quindío', 'Departamento del eje cafetero');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `favorito`
--

CREATE TABLE `favorito` (
  `idFavorito` int(11) NOT NULL,
  `iduser` int(11) NOT NULL,
  `idLocal` int(11) DEFAULT NULL,
  `idProducto` int(11) DEFAULT NULL,
  `fechaAgregado` datetime NOT NULL DEFAULT current_timestamp()
) ;

--
-- Volcado de datos para la tabla `favorito`
--

INSERT INTO `favorito` (`idFavorito`, `iduser`, `idLocal`, `idProducto`, `fechaAgregado`) VALUES
(2, 1, 1, NULL, '2026-06-17 05:13:21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horario`
--

CREATE TABLE `horario` (
  `idHorario` int(11) NOT NULL,
  `horarioApertura` time NOT NULL,
  `horarioCierre` time NOT NULL,
  `descansoOpc` varchar(50) DEFAULT NULL,
  `diasDisponibles` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `horario`
--

INSERT INTO `horario` (`idHorario`, `horarioApertura`, `horarioCierre`, `descansoOpc`, `diasDisponibles`) VALUES
(1, '08:00:00', '18:00:00', '', 'LUN,MAR,MIE,JUE,VIE,SAB');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `local`
--

CREATE TABLE `local` (
  `idLocal` int(11) NOT NULL,
  `nomLocal` varchar(100) NOT NULL,
  `describLocal` varchar(300) DEFAULT NULL,
  `infoContacto` varchar(200) DEFAULT NULL,
  `ubiLocal` varchar(150) DEFAULT NULL,
  `urlWhatsapp` varchar(20) NOT NULL,
  `estado` enum('activo','inactivo','pendiente','rechazado') NOT NULL DEFAULT 'pendiente',
  `fechaRegistro` datetime NOT NULL DEFAULT current_timestamp(),
  `idHorario` int(11) DEFAULT NULL,
  `idVendedor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `local`
--

INSERT INTO `local` (`idLocal`, `nomLocal`, `describLocal`, `infoContacto`, `ubiLocal`, `urlWhatsapp`, `estado`, `fechaRegistro`, `idHorario`, `idVendedor`) VALUES
(1, 'el niche store', 'adcsdxc', NULL, 'Local 5', '+57 3000000001', 'activo', '2026-06-17 03:47:19', 1, 15);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificacion`
--

CREATE TABLE `notificacion` (
  `idNotificacion` int(11) NOT NULL,
  `iduser` int(11) NOT NULL,
  `tipoNotific` varchar(30) NOT NULL,
  `mensaje` varchar(200) NOT NULL,
  `urlDestino` varchar(100) DEFAULT NULL,
  `estado` enum('no_leida','leida','eliminada') NOT NULL DEFAULT 'no_leida',
  `fecha` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `origenusuario`
--

CREATE TABLE `origenusuario` (
  `idOrigenUser` int(11) NOT NULL,
  `idciud` int(11) NOT NULL,
  `idDepart` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `idProducto` int(11) NOT NULL,
  `nomProducto` varchar(80) NOT NULL,
  `describProducto` varchar(200) DEFAULT NULL,
  `fotosUrl` text DEFAULT NULL,
  `idcategoria` int(11) NOT NULL,
  `idLocal` int(11) NOT NULL,
  `tipoProducto` varchar(50) DEFAULT NULL,
  `talla` varchar(30) DEFAULT NULL,
  `estado` enum('disponible','agotado','inactivo') NOT NULL DEFAULT 'disponible',
  `precio` decimal(10,2) NOT NULL CHECK (`precio` >= 0),
  `seccion` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`idProducto`, `nomProducto`, `describProducto`, `fotosUrl`, `idcategoria`, `idLocal`, `tipoProducto`, `talla`, `estado`, `precio`, `seccion`) VALUES
(1, 'nike air univalle', '', '[\"https:\\/\\/gustostore.com.co\\/producto\\/nike-air-force-one-supreme-2\\/\"]', 6, 1, NULL, '44', 'disponible', 160000.00, 'Destacados'),
(2, 'bolso', 'bolso', NULL, 5, 1, NULL, '44', 'inactivo', 70000.00, 'Remates');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `idRol` int(11) NOT NULL,
  `nomRol` varchar(30) NOT NULL,
  `describRol` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`idRol`, `nomRol`, `describRol`) VALUES
(1, 'Comprador', 'Usuario que navega y contacta vendedores'),
(2, 'Vendedor', 'Usuario que gestiona un local y sus productos'),
(3, 'Administrador', 'Gestiona usuarios, locales y configuración de la plataforma');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipodocumento`
--

CREATE TABLE `tipodocumento` (
  `idTipDocu` int(11) NOT NULL,
  `nomTipDocu` varchar(30) NOT NULL,
  `describTipDocu` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipodocumento`
--

INSERT INTO `tipodocumento` (`idTipDocu`, `nomTipDocu`, `describTipDocu`) VALUES
(1, 'CC', 'Cédula de Ciudadanía'),
(2, 'TI', 'Tarjeta de Identidad'),
(3, 'CE', 'Cédula de Extranjería'),
(4, 'PA', 'Pasaporte'),
(5, 'NIT', 'Número de Identificación Tributaria');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `iduser` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `passwordHash` varchar(255) NOT NULL,
  `primerNombre` varchar(50) NOT NULL,
  `segundoNombre` varchar(50) DEFAULT NULL,
  `primerApellido` varchar(50) NOT NULL,
  `segundoApellido` varchar(50) DEFAULT NULL,
  `direccion` varchar(150) DEFAULT NULL,
  `celular` varchar(15) DEFAULT NULL,
  `idTipDocu` int(11) NOT NULL,
  `idRol` int(11) NOT NULL DEFAULT 1,
  `idOrigenUser` int(11) DEFAULT NULL,
  `fechaRegistro` datetime NOT NULL DEFAULT current_timestamp(),
  `estado` enum('activo','inactivo','suspendido') NOT NULL DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`iduser`, `username`, `email`, `passwordHash`, `primerNombre`, `segundoNombre`, `primerApellido`, `segundoApellido`, `direccion`, `celular`, `idTipDocu`, `idRol`, `idOrigenUser`, `fechaRegistro`, `estado`) VALUES
(1, 'josdav57', 'demo@example.com', 'HASH_DE_EJEMPLO_NO_VALIDO', 'ma', NULL, 'shbja', NULL, '', '+57 3000000000', 1, 3, NULL, '2026-06-17 03:46:31', 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vendedor`
--

CREATE TABLE `vendedor` (
  `idVendedor` int(11) NOT NULL,
  `iduser` int(11) NOT NULL,
  `nomVendedor` varchar(80) NOT NULL,
  `infoContacto` varchar(150) DEFAULT NULL,
  `estado` enum('activo','inactivo','suspendido') NOT NULL DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `vendedor`
--

INSERT INTO `vendedor` (`idVendedor`, `iduser`, `nomVendedor`, `infoContacto`, `estado`) VALUES
(15, 1, 'ma', NULL, 'activo');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`idcategoria`),
  ADD UNIQUE KEY `nomCategoria` (`nomCategoria`);

--
-- Indices de la tabla `ciudad`
--
ALTER TABLE `ciudad`
  ADD PRIMARY KEY (`idciud`),
  ADD KEY `FK_Ciudad_Departamento` (`idDepart`);

--
-- Indices de la tabla `departamento`
--
ALTER TABLE `departamento`
  ADD PRIMARY KEY (`idDepart`);

--
-- Indices de la tabla `favorito`
--
ALTER TABLE `favorito`
  ADD PRIMARY KEY (`idFavorito`),
  ADD KEY `FK_Favorito_Local` (`idLocal`),
  ADD KEY `FK_Favorito_Producto` (`idProducto`),
  ADD KEY `IDX_Favorito_iduser` (`iduser`);

--
-- Indices de la tabla `horario`
--
ALTER TABLE `horario`
  ADD PRIMARY KEY (`idHorario`);

--
-- Indices de la tabla `local`
--
ALTER TABLE `local`
  ADD PRIMARY KEY (`idLocal`),
  ADD KEY `FK_Local_Horario` (`idHorario`),
  ADD KEY `IDX_Local_estado` (`estado`),
  ADD KEY `IDX_Local_idVendedor` (`idVendedor`);

--
-- Indices de la tabla `notificacion`
--
ALTER TABLE `notificacion`
  ADD PRIMARY KEY (`idNotificacion`),
  ADD KEY `IDX_Notif_iduser` (`iduser`),
  ADD KEY `IDX_Notif_estado` (`estado`);

--
-- Indices de la tabla `origenusuario`
--
ALTER TABLE `origenusuario`
  ADD PRIMARY KEY (`idOrigenUser`),
  ADD KEY `FK_OrigenUsuario_Ciudad` (`idciud`),
  ADD KEY `FK_OrigenUsuario_Departamento` (`idDepart`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`idProducto`),
  ADD KEY `IDX_Producto_idLocal` (`idLocal`),
  ADD KEY `IDX_Producto_idcateg` (`idcategoria`),
  ADD KEY `IDX_Producto_estado` (`estado`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`idRol`),
  ADD UNIQUE KEY `nomRol` (`nomRol`);

--
-- Indices de la tabla `tipodocumento`
--
ALTER TABLE `tipodocumento`
  ADD PRIMARY KEY (`idTipDocu`),
  ADD UNIQUE KEY `nomTipDocu` (`nomTipDocu`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`iduser`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `FK_Usuario_TipoDocumento` (`idTipDocu`),
  ADD KEY `FK_Usuario_Rol` (`idRol`),
  ADD KEY `FK_Usuario_OrigenUsuario` (`idOrigenUser`),
  ADD KEY `IDX_Usuario_email` (`email`),
  ADD KEY `IDX_Usuario_estado` (`estado`);

--
-- Indices de la tabla `vendedor`
--
ALTER TABLE `vendedor`
  ADD PRIMARY KEY (`idVendedor`),
  ADD UNIQUE KEY `iduser` (`iduser`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `idcategoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `ciudad`
--
ALTER TABLE `ciudad`
  MODIFY `idciud` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `departamento`
--
ALTER TABLE `departamento`
  MODIFY `idDepart` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `favorito`
--
ALTER TABLE `favorito`
  MODIFY `idFavorito` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `horario`
--
ALTER TABLE `horario`
  MODIFY `idHorario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `local`
--
ALTER TABLE `local`
  MODIFY `idLocal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `notificacion`
--
ALTER TABLE `notificacion`
  MODIFY `idNotificacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `origenusuario`
--
ALTER TABLE `origenusuario`
  MODIFY `idOrigenUser` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `idProducto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `idRol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tipodocumento`
--
ALTER TABLE `tipodocumento`
  MODIFY `idTipDocu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `iduser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `vendedor`
--
ALTER TABLE `vendedor`
  MODIFY `idVendedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `ciudad`
--
ALTER TABLE `ciudad`
  ADD CONSTRAINT `FK_Ciudad_Departamento` FOREIGN KEY (`idDepart`) REFERENCES `departamento` (`idDepart`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `favorito`
--
ALTER TABLE `favorito`
  ADD CONSTRAINT `FK_Favorito_Local` FOREIGN KEY (`idLocal`) REFERENCES `local` (`idLocal`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_Favorito_Producto` FOREIGN KEY (`idProducto`) REFERENCES `producto` (`idProducto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_Favorito_Usuario` FOREIGN KEY (`iduser`) REFERENCES `usuario` (`iduser`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `local`
--
ALTER TABLE `local`
  ADD CONSTRAINT `FK_Local_Horario` FOREIGN KEY (`idHorario`) REFERENCES `horario` (`idHorario`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_Local_Vendedor` FOREIGN KEY (`idVendedor`) REFERENCES `vendedor` (`idVendedor`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `notificacion`
--
ALTER TABLE `notificacion`
  ADD CONSTRAINT `FK_Notificacion_Usuario` FOREIGN KEY (`iduser`) REFERENCES `usuario` (`iduser`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `origenusuario`
--
ALTER TABLE `origenusuario`
  ADD CONSTRAINT `FK_OrigenUsuario_Ciudad` FOREIGN KEY (`idciud`) REFERENCES `ciudad` (`idciud`) ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_OrigenUsuario_Departamento` FOREIGN KEY (`idDepart`) REFERENCES `departamento` (`idDepart`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `FK_Producto_Categoria` FOREIGN KEY (`idcategoria`) REFERENCES `categoria` (`idcategoria`) ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_Producto_Local` FOREIGN KEY (`idLocal`) REFERENCES `local` (`idLocal`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `FK_Usuario_OrigenUsuario` FOREIGN KEY (`idOrigenUser`) REFERENCES `origenusuario` (`idOrigenUser`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_Usuario_Rol` FOREIGN KEY (`idRol`) REFERENCES `rol` (`idRol`) ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_Usuario_TipoDocumento` FOREIGN KEY (`idTipDocu`) REFERENCES `tipodocumento` (`idTipDocu`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `vendedor`
--
ALTER TABLE `vendedor`
  ADD CONSTRAINT `FK_Vendedor_Usuario` FOREIGN KEY (`iduser`) REFERENCES `usuario` (`iduser`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
