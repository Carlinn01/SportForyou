-- SQL para criar a tabela de compartilhamentos
-- Execute este SQL no seu banco de dados MySQL

CREATE TABLE IF NOT EXISTS `compartilhamentos` (
  `idcompartilhamento` INT(11) NOT NULL AUTO_INCREMENT,
  `idusuario` INT(11) NOT NULL,
  `idpostagem` INT(11) NOT NULL,
  `data_compartilhamento` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`idcompartilhamento`),
  KEY `idusuario` (`idusuario`),
  KEY `idpostagem` (`idpostagem`),
  CONSTRAINT `compartilhamentos_ibfk_1` FOREIGN KEY (`idusuario`) REFERENCES `usuarios` (`idusuarios`) ON DELETE CASCADE,
  CONSTRAINT `compartilhamentos_ibfk_2` FOREIGN KEY (`idpostagem`) REFERENCES `postagens` (`idpostagem`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

