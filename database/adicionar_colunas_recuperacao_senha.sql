-- Adiciona colunas para recuperação de senha na tabela usuarios
-- Execute este script no seu banco de dados MySQL
-- IMPORTANTE: Se alguma coluna já existir, você verá um erro 1060 (Duplicate column name)
-- Esse erro é normal e pode ser ignorado - significa que a coluna já existe

-- Adiciona token_recuperacao
-- Se a coluna já existir, você verá: Error Code: 1060. Duplicate column name 'token_recuperacao'
-- Isso é normal - apenas ignore o erro e continue
ALTER TABLE usuarios
ADD COLUMN token_recuperacao VARCHAR(100) DEFAULT NULL;

-- Adiciona token_expira
-- Se a coluna já existir, você verá: Error Code: 1060. Duplicate column name 'token_expira'
-- Isso é normal - apenas ignore o erro e continue
ALTER TABLE usuarios
ADD COLUMN token_expira DATETIME DEFAULT NULL;

-- ============================================
-- VERIFICAÇÃO (opcional - execute para confirmar)
-- ============================================
-- Execute este comando para verificar se as colunas foram criadas:
-- SELECT COLUMN_NAME, DATA_TYPE, COLUMN_DEFAULT 
-- FROM INFORMATION_SCHEMA.COLUMNS 
-- WHERE TABLE_SCHEMA = DATABASE() 
-- AND TABLE_NAME = 'usuarios' 
-- AND COLUMN_NAME IN ('token_recuperacao', 'token_expira');
