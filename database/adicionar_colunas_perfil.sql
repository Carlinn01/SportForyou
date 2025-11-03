-- Adiciona colunas para completar RF2
-- MySQL não suporta IF NOT EXISTS diretamente, então execute um comando por vez
-- Se alguma coluna já existir, ignore o erro ou execute apenas os que faltam

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

