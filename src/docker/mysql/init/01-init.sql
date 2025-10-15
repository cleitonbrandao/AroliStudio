-- Script de inicialização do banco de dados
-- Este arquivo será executado automaticamente quando o container for criado

-- Criar o banco de dados se não existir
CREATE DATABASE IF NOT EXISTS aroli_studio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Usar o banco de dados
USE aroli_studio;

-- Configurações adicionais para o MySQL
SET GLOBAL sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO';

-- Log de inicialização
SELECT 'Banco de dados aroli_studio inicializado com sucesso!' as status;
