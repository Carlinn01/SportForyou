-- SQL para criar a tabela de eventos
-- Execute este SQL no seu banco de dados MySQL

CREATE TABLE IF NOT EXISTS `eventos` (
  `idevento` INT(11) NOT NULL AUTO_INCREMENT,
  `organizador_id` INT(11) NOT NULL,
  `titulo` VARCHAR(255) NOT NULL,
  `descricao` TEXT DEFAULT NULL,
  `tipo_esporte` VARCHAR(100) NOT NULL,
  `data_evento` DATE NOT NULL,
  `hora_evento` TIME DEFAULT NULL,
  `local` VARCHAR(255) NOT NULL,
  `cidade` VARCHAR(100) DEFAULT NULL,
  `estado` VARCHAR(2) DEFAULT NULL,
  `foto` VARCHAR(255) DEFAULT NULL,
  `criado_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `atualizado_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  PRIMARY KEY (`idevento`),
  KEY `organizador_id` (`organizador_id`),
  KEY `data_evento` (`data_evento`),
  KEY `tipo_esporte` (`tipo_esporte`),
  CONSTRAINT `eventos_ibfk_1` FOREIGN KEY (`organizador_id`) REFERENCES `usuarios` (`idusuarios`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela para registrar interessados nos eventos
CREATE TABLE IF NOT EXISTS `eventos_interessados` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `evento_id` INT(11) NOT NULL,
  `usuario_id` INT(11) NOT NULL,
  `criado_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_interesse` (`evento_id`, `usuario_id`),
  KEY `evento_id` (`evento_id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `eventos_interessados_ibfk_1` FOREIGN KEY (`evento_id`) REFERENCES `eventos` (`idevento`) ON DELETE CASCADE,
  CONSTRAINT `eventos_interessados_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`idusuarios`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

