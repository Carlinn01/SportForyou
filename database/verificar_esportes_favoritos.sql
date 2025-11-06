-- Script para verificar e criar as colunas de esportes favoritos se não existirem

-- Verifica se a coluna esportes_favoritos existe
SELECT COLUMN_NAME 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'usuarios' 
AND COLUMN_NAME = 'esportes_favoritos';

-- Se não existir, execute:
ALTER TABLE usuarios 
ADD COLUMN esportes_favoritos TEXT DEFAULT NULL;

-- Verifica se a coluna esportes_detalhados existe
SELECT COLUMN_NAME 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'usuarios' 
AND COLUMN_NAME = 'esportes_detalhados';

-- Se não existir, execute:
ALTER TABLE usuarios 
ADD COLUMN esportes_detalhados TEXT DEFAULT NULL AFTER esportes_favoritos;

-- Verifica os dados salvos (substitua 1 pelo ID do seu usuário)
SELECT idusuarios, nome, esportes_favoritos, esportes_detalhados 
FROM usuarios 
WHERE idusuarios = 1;

