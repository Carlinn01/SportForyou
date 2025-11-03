-- Script para verificar e adicionar colunas que podem estar faltando
-- Execute este script no seu banco de dados MySQL
-- Se alguma coluna já existir, o erro será informado mas você pode ignorar

-- ============================================
-- RECUPERAÇÃO DE SENHA
-- ============================================

-- Verifica se token_recuperacao existe
SELECT COUNT(*) as existe FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'usuarios' 
AND COLUMN_NAME = 'token_recuperacao';

-- Se não existir (retornar 0), execute:
-- ALTER TABLE usuarios ADD COLUMN token_recuperacao VARCHAR(100) DEFAULT NULL;

-- Verifica se token_expira existe
SELECT COUNT(*) as existe FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'usuarios' 
AND COLUMN_NAME = 'token_expira';

-- Se não existir (retornar 0), execute:
-- ALTER TABLE usuarios ADD COLUMN token_expira DATETIME DEFAULT NULL;

-- ============================================
-- OUTRAS COLUNAS (se necessário)
-- ============================================

-- Verifica se tipo existe na tabela postagens
SELECT COUNT(*) as existe FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'postagens' 
AND COLUMN_NAME = 'tipo';

-- Se não existir, execute:
-- ALTER TABLE postagens ADD COLUMN tipo VARCHAR(20) DEFAULT 'imagem' AFTER foto;

-- Verifica se descricao_pessoal existe na tabela usuarios
SELECT COUNT(*) as existe FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'usuarios' 
AND COLUMN_NAME = 'descricao_pessoal';

-- Se não existir, execute:
-- ALTER TABLE usuarios ADD COLUMN descricao_pessoal TEXT DEFAULT NULL AFTER objetivos;

