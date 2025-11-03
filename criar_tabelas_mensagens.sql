-- SQL para criar as tabelas do sistema de mensagens
-- Execute este SQL no seu banco de dados MySQL

-- Tabela de conversas (direct messages)
CREATE TABLE IF NOT EXISTS `conversas` (
  `idconversa` INT(11) NOT NULL AUTO_INCREMENT,
  `usuario1_id` INT(11) NOT NULL,
  `usuario2_id` INT(11) NOT NULL,
  `ultima_mensagem_id` INT(11) DEFAULT NULL,
  `criado_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `atualizado_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  PRIMARY KEY (`idconversa`),
  UNIQUE KEY `unique_conversa` (`usuario1_id`, `usuario2_id`),
  KEY `usuario1_id` (`usuario1_id`),
  KEY `usuario2_id` (`usuario2_id`),
  CONSTRAINT `conversas_ibfk_1` FOREIGN KEY (`usuario1_id`) REFERENCES `usuarios` (`idusuarios`) ON DELETE CASCADE,
  CONSTRAINT `conversas_ibfk_2` FOREIGN KEY (`usuario2_id`) REFERENCES `usuarios` (`idusuarios`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de mensagens
CREATE TABLE IF NOT EXISTS `mensagens` (
  `idmensagem` INT(11) NOT NULL AUTO_INCREMENT,
  `conversa_id` INT(11) NOT NULL,
  `remetente_id` INT(11) NOT NULL,
  `conteudo` TEXT NOT NULL,
  `status` ENUM('enviada', 'entregue', 'lida') DEFAULT 'enviada',
  `anexo_url` VARCHAR(255) DEFAULT NULL,
  `criado_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`idmensagem`),
  KEY `conversa_id` (`conversa_id`),
  KEY `remetente_id` (`remetente_id`),
  KEY `criado_em` (`criado_em`),
  CONSTRAINT `mensagens_ibfk_1` FOREIGN KEY (`conversa_id`) REFERENCES `conversas` (`idconversa`) ON DELETE CASCADE,
  CONSTRAINT `mensagens_ibfk_2` FOREIGN KEY (`remetente_id`) REFERENCES `usuarios` (`idusuarios`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela para marcar mensagens como lidas
CREATE TABLE IF NOT EXISTS `mensagens_lidas` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `mensagem_id` INT(11) NOT NULL,
  `usuario_id` INT(11) NOT NULL,
  `lida_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_lida` (`mensagem_id`, `usuario_id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `mensagens_lidas_ibfk_1` FOREIGN KEY (`mensagem_id`) REFERENCES `mensagens` (`idmensagem`) ON DELETE CASCADE,
  CONSTRAINT `mensagens_lidas_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`idusuarios`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Índice adicional para melhorar performance nas consultas
ALTER TABLE `conversas` ADD INDEX `idx_atualizado_em` (`atualizado_em`);

