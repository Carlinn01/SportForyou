-- Script SQL corrigido para adicionar colunas
-- MySQL não suporta IF NOT EXISTS diretamente, então execute os comandos um por vez
-- Se alguma coluna já existir, ignore o erro

-- ============================================
-- 1. Adicionar coluna tipo na tabela postagens
-- ============================================
-- Execute este comando (se a coluna já existir, ignore o erro):
ALTER TABLE postagens 
ADD COLUMN tipo VARCHAR(20) DEFAULT 'imagem' AFTER foto;

-- Atualiza registros existentes (assume que são imagens)
UPDATE postagens SET tipo = 'imagem' WHERE (tipo IS NULL OR tipo = '') AND foto IS NOT NULL;

-- ============================================
-- 2. Adicionar colunas na tabela usuarios
-- ============================================
-- Execute os comandos um por vez (se alguma coluna já existir, ignore o erro):

-- 2.1. Adiciona descricao_pessoal
ALTER TABLE usuarios 
ADD COLUMN descricao_pessoal TEXT DEFAULT NULL AFTER objetivos;

-- 2.2. Adiciona tipo_treino_favorito
ALTER TABLE usuarios 
ADD COLUMN tipo_treino_favorito VARCHAR(100) DEFAULT NULL AFTER descricao_pessoal;

-- 2.3. Adiciona esportes_detalhados
ALTER TABLE usuarios 
ADD COLUMN esportes_detalhados TEXT DEFAULT NULL AFTER esportes_favoritos;

-- Estrutura de esportes_detalhados será JSON:
-- [{"esporte": "Futebol", "nivel": "Intermediário", "frequencia": "2-3x/semana"}, ...]

