-- ============================================================
--  GALEZARZAL — Tabla de mensajes de contacto (Ayuda y Contacto)
--  Solo para quien YA tiene la base importada antes de este cambio.
--  Instalaciones nuevas: galezarzalBD.sql ya la incluye.
--  Ejecutar en phpMyAdmin con la base `galezarzal` seleccionada.
-- ============================================================
CREATE TABLE IF NOT EXISTS `mensajecontacto` (
  `idMensaje` int(11) NOT NULL AUTO_INCREMENT,
  `iduser` int(11) DEFAULT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `asunto` varchar(150) DEFAULT NULL,
  `mensaje` text NOT NULL,
  `estado` enum('nuevo','leido') NOT NULL DEFAULT 'nuevo',
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idMensaje`),
  KEY `IDX_Mensaje_iduser` (`iduser`),
  KEY `IDX_Mensaje_estado` (`estado`),
  CONSTRAINT `FK_Mensaje_Usuario` FOREIGN KEY (`iduser`) REFERENCES `usuario` (`iduser`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
