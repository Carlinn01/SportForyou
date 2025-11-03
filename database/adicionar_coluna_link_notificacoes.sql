-- SQL para adicionar a coluna link na tabela notificacoes
-- Execute este SQL no seu banco de dados MySQL

-- Verifica se a coluna já existe antes de adicionar
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'notificacoes' 
AND COLUMN_NAME = 'link';

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `notificacoes` ADD COLUMN `link` VARCHAR(255) DEFAULT NULL AFTER `mensagem`',
    'SELECT "A coluna link já existe na tabela notificacoes" AS resultado');
    
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

