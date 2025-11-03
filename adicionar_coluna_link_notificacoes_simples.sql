-- SQL simples para adicionar a coluna link na tabela notificacoes
-- Execute este SQL no seu banco de dados MySQL
-- Se der erro dizendo que a coluna já existe, apenas ignore o erro

ALTER TABLE `notificacoes` 
ADD COLUMN `link` VARCHAR(255) DEFAULT NULL 
AFTER `mensagem`;

