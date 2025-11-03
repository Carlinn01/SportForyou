-- Script para verificar e criar a coluna esportes_detalhados se não existir

-- Verifica se a coluna existe
SELECT COLUMN_NAME 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'usuarios' 
AND COLUMN_NAME = 'esportes_detalhados';

-- Se a coluna não existir, execute este comando:
-- ALTER TABLE usuarios 
-- ADD COLUMN esportes_detalhados TEXT DEFAULT NULL AFTER esportes_favoritos;

-- Verifica se a coluna esportes_favoritos existe também
SELECT COLUMN_NAME 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'usuarios' 
AND COLUMN_NAME = 'esportes_favoritos';

-- Se a coluna esportes_favoritos não existir, execute:
-- ALTER TABLE usuarios 
-- ADD COLUMN esportes_favoritos TEXT DEFAULT NULL;

