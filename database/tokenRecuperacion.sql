-- ============================================================
--  GALEZARZAL — Tabla de tokens de recuperación de contraseña
--  Solo para quien YA tiene la base importada antes de este cambio.
--  Instalaciones nuevas: galezarzalBD.sql ya la incluye.
--  Ejecutar en phpMyAdmin con la base `galezarzal` seleccionada.
-- ============================================================
CREATE TABLE IF NOT EXISTS `tokenrecuperacion` (
  `idToken` int(11) NOT NULL AUTO_INCREMENT,
  `iduser` int(11) NOT NULL,
  `tokenHash` char(64) NOT NULL,
  `fechaCreacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fechaExpira` datetime NOT NULL,
  `fechaUso` datetime DEFAULT NULL,
  PRIMARY KEY (`idToken`),
  UNIQUE KEY `UQ_Token_hash` (`tokenHash`),
  KEY `IDX_Token_iduser` (`iduser`),
  CONSTRAINT `FK_Token_Usuario` FOREIGN KEY (`iduser`) REFERENCES `usuario` (`iduser`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
