-- Script SQL para adicionar colunas com verificação de existência
-- Compatível com MySQL que não suporta IF NOT EXISTS diretamente

-- ============================================
-- Adicionar coluna tipo na tabela postagens
-- ============================================
-- Verifique se a coluna existe antes de executar:
-- SHOW COLUMNS FROM postagens LIKE 'tipo';

-- Se não existir, execute:
ALTER TABLE postagens 
ADD COLUMN tipo VARCHAR(20) DEFAULT 'imagem' AFTER foto;

-- Atualiza registros existentes (assume que são imagens)
UPDATE postagens SET tipo = 'imagem' WHERE (tipo IS NULL OR tipo = '') AND foto IS NOT NULL;

-- ============================================
-- Adicionar colunas na tabela usuarios
-- ============================================
-- Verifique se as colunas existem antes de executar:
-- SHOW COLUMNS FROM usuarios LIKE 'descricao_pessoal';
-- SHOW COLUMNS FROM usuarios LIKE 'tipo_treino_favorito';
-- SHOW COLUMNS FROM usuarios LIKE 'esportes_detalhados';

-- Se não existirem, execute um por vez:

-- 1. Adiciona descricao_pessoal
ALTER TABLE usuarios 
ADD COLUMN descricao_pessoal TEXT DEFAULT NULL AFTER objetivos;

-- 2. Adiciona tipo_treino_favorito
ALTER TABLE usuarios 
ADD COLUMN tipo_treino_favorito VARCHAR(100) DEFAULT NULL AFTER descricao_pessoal;

-- 3. Adiciona esportes_detalhados
ALTER TABLE usuarios 
ADD COLUMN esportes_detalhados TEXT DEFAULT NULL AFTER esportes_favoritos;

-- Estrutura de esportes_detalhados será JSON:
-- [{"esporte": "Futebol", "nivel": "Intermediário", "frequencia": "2-3x/semana"}, ...]

