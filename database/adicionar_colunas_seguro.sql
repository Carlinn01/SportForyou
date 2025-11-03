-- Script SQL seguro para adicionar colunas apenas se não existirem
-- Execute este script no seu banco de dados MySQL

-- ============================================
-- RECUPERAÇÃO DE SENHA
-- ============================================

-- Adiciona token_recuperacao se não existir
SET @coluna_existe = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'usuarios' 
    AND COLUMN_NAME = 'token_recuperacao'
);

SET @sql = IF(@coluna_existe = 0,
    'ALTER TABLE usuarios ADD COLUMN token_recuperacao VARCHAR(100) DEFAULT NULL',
    'SELECT "Coluna token_recuperacao já existe" AS mensagem'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Adiciona token_expira se não existir
SET @coluna_existe = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'usuarios' 
    AND COLUMN_NAME = 'token_expira'
);

SET @sql = IF(@coluna_existe = 0,
    'ALTER TABLE usuarios ADD COLUMN token_expira DATETIME DEFAULT NULL',
    'SELECT "Coluna token_expira já existe" AS mensagem'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

