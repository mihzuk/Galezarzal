-- ============================================================
--  GALEZARZAL — Tabla de configuración de la plataforma (RF-22)
--  Solo para quien YA tiene la base importada antes de este cambio.
--  Instalaciones nuevas: galezarzalBD.sql ya la incluye.
--  Ejecutar en phpMyAdmin con la base `galezarzal` seleccionada.
-- ============================================================
CREATE TABLE IF NOT EXISTS `configuracion` (
  `clave` varchar(50) NOT NULL,
  `valor` varchar(255) NOT NULL,
  PRIMARY KEY (`clave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `configuracion` (`clave`, `valor`) VALUES
('nombre_sitio', 'GaleZarzal'),
('modo_mantenimiento', '0'),
('registro_abierto', '1'),
('aprobacion_manual', '0')
ON DUPLICATE KEY UPDATE `clave` = `clave`;