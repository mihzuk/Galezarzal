CREATE TABLE IF NOT EXISTS `preferencias_usuario` (
    `iduser` INT(11) NOT NULL,
    `notif_productos` TINYINT(1) NOT NULL DEFAULT 1,
    `notif_respuestas` TINYINT(1) NOT NULL DEFAULT 1,
    `notif_novedades` TINYINT(1) NOT NULL DEFAULT 1,
    `notif_correo` TINYINT(1) NOT NULL DEFAULT 1,
    `priv_nombre_completo` TINYINT(1) NOT NULL DEFAULT 1,
    `priv_resenas_visibles` TINYINT(1) NOT NULL DEFAULT 1,
    `priv_visible_vendedores` TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`iduser`),
    CONSTRAINT `FK_Preferencias_Usuario`
        FOREIGN KEY (`iduser`)
        REFERENCES `usuario` (`iduser`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
