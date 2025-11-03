-- Adiciona coluna tipo na tabela postagens para diferenciar imagens e vídeos
-- MySQL não suporta IF NOT EXISTS diretamente, então verifique manualmente se a coluna existe antes de executar

-- Se a coluna não existir, execute este comando:
ALTER TABLE postagens 
ADD COLUMN tipo VARCHAR(20) DEFAULT 'imagem' AFTER foto;

-- Atualiza registros existentes (assume que são imagens)
UPDATE postagens SET tipo = 'imagem' WHERE (tipo IS NULL OR tipo = '') AND foto IS NOT NULL;

