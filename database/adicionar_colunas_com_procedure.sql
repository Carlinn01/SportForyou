-- Script SQL usando procedure para adicionar colunas apenas se não existirem
-- Compatível com MySQL

DELIMITER //

-- Procedure para adicionar coluna tipo em postagens
DROP PROCEDURE IF EXISTS adicionar_coluna_tipo_postagens//
CREATE PROCEDURE adicionar_coluna_tipo_postagens()
BEGIN
    DECLARE column_exists INT DEFAULT 0;
    
    SELECT COUNT(*) INTO column_exists
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'postagens'
    AND COLUMN_NAME = 'tipo';
    
    IF column_exists = 0 THEN
        ALTER TABLE postagens 
        ADD COLUMN tipo VARCHAR(20) DEFAULT 'imagem' AFTER foto;
        
        UPDATE postagens SET tipo = 'imagem' WHERE (tipo IS NULL OR tipo = '') AND foto IS NOT NULL;
    END IF;
END//

-- Procedure para adicionar colunas em usuarios
DROP PROCEDURE IF EXISTS adicionar_colunas_perfil//
CREATE PROCEDURE adicionar_colunas_perfil()
BEGIN
    DECLARE column_exists INT DEFAULT 0;
    
    -- Adiciona descricao_pessoal
    SELECT COUNT(*) INTO column_exists
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'usuarios'
    AND COLUMN_NAME = 'descricao_pessoal';
    
    IF column_exists = 0 THEN
        ALTER TABLE usuarios 
        ADD COLUMN descricao_pessoal TEXT DEFAULT NULL AFTER objetivos;
    END IF;
    
    -- Adiciona tipo_treino_favorito
    SET column_exists = 0;
    SELECT COUNT(*) INTO column_exists
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'usuarios'
    AND COLUMN_NAME = 'tipo_treino_favorito';
    
    IF column_exists = 0 THEN
        ALTER TABLE usuarios 
        ADD COLUMN tipo_treino_favorito VARCHAR(100) DEFAULT NULL AFTER descricao_pessoal;
    END IF;
    
    -- Adiciona esportes_detalhados
    SET column_exists = 0;
    SELECT COUNT(*) INTO column_exists
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'usuarios'
    AND COLUMN_NAME = 'esportes_detalhados';
    
    IF column_exists = 0 THEN
        ALTER TABLE usuarios 
        ADD COLUMN esportes_detalhados TEXT DEFAULT NULL AFTER esportes_favoritos;
    END IF;
END//

DELIMITER ;

-- Executa as procedures
CALL adicionar_coluna_tipo_postagens();
CALL adicionar_colunas_perfil();

-- Remove as procedures após uso (opcional)
DROP PROCEDURE IF EXISTS adicionar_coluna_tipo_postagens;
DROP PROCEDURE IF EXISTS adicionar_colunas_perfil;

