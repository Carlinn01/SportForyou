<?php
include("../login/incs/valida-sessao.php");
require_once "../login/src/ConexaoBD.php";

$idusuario_logado = $_SESSION['idusuarios'];
$conexao = ConexaoBD::conectar();

// Garante que a conexão usa UTF-8
try {
    $conexao->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
} catch (PDOException $e) {
    // Ignora se falhar
}

// Função para verificar se uma coluna existe na tabela
function colunaExiste($pdo, $tabela, $coluna) {
    try {
        $sql = "SHOW COLUMNS FROM $tabela LIKE '$coluna'";
        $stmt = $pdo->query($sql);
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        return false;
    }
}

// Função para criar a tabela de erros se não existir
function criarTabelaErros($pdo) {
    try {
        $sql = "CREATE TABLE IF NOT EXISTS erros_reportados (
            id INT AUTO_INCREMENT PRIMARY KEY,
            idusuario INT NOT NULL,
            titulo VARCHAR(100) NOT NULL,
            categoria VARCHAR(50) NOT NULL,
            descricao TEXT NOT NULL,
            url_pagina VARCHAR(255) DEFAULT NULL,
            status ENUM('pendente', 'em_analise', 'resolvido', 'descartado') DEFAULT 'pendente',
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_usuario (idusuario),
            INDEX idx_status (status),
            INDEX idx_criado_em (criado_em)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        $pdo->exec($sql);
        return true;
    } catch (PDOException $e) {
        error_log("Erro ao criar tabela erros_reportados: " . $e->getMessage());
        return false;
    }
}

// Verifica se a tabela existe, se não, cria
try {
    $sqlCheck = "SHOW TABLES LIKE 'erros_reportados'";
    $stmtCheck = $conexao->query($sqlCheck);
    if ($stmtCheck->rowCount() == 0) {
        criarTabelaErros($conexao);
    }
} catch (PDOException $e) {
    // Tenta criar a tabela
    criarTabelaErros($conexao);
}

// Processa o formulário
$titulo = trim($_POST['titulo'] ?? '');
$categoria = trim($_POST['categoria'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');
$url_pagina = trim($_POST['url_pagina'] ?? '');

// Validações
if (empty($titulo) || empty($categoria) || empty($descricao)) {
    $_SESSION['msg'] = 'Por favor, preencha todos os campos obrigatórios.';
    $_SESSION['msg_tipo'] = 'erro';
    header("Location: ../pages/reportar_erro.php");
    exit;
}

if (strlen($titulo) > 100) {
    $_SESSION['msg'] = 'O título deve ter no máximo 100 caracteres.';
    $_SESSION['msg_tipo'] = 'erro';
    header("Location: ../pages/reportar_erro.php");
    exit;
}

if (strlen($descricao) > 1000) {
    $_SESSION['msg'] = 'A descrição deve ter no máximo 1000 caracteres.';
    $_SESSION['msg_tipo'] = 'erro';
    header("Location: ../pages/reportar_erro.php");
    exit;
}

// Insere o erro no banco de dados
try {
    $sql = "INSERT INTO erros_reportados (idusuario, titulo, categoria, descricao, url_pagina, status) 
            VALUES (?, ?, ?, ?, ?, 'pendente')";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$idusuario_logado, $titulo, $categoria, $descricao, $url_pagina ?: null]);
    
    $_SESSION['msg'] = 'Erro reportado com sucesso! Obrigado por nos ajudar a melhorar.';
    $_SESSION['msg_tipo'] = 'sucesso';
    
} catch (PDOException $e) {
    error_log("Erro ao reportar erro: " . $e->getMessage());
    $_SESSION['msg'] = 'Erro ao enviar reporte. Tente novamente.';
    $_SESSION['msg_tipo'] = 'erro';
}

header("Location: ../pages/reportar_erro.php");
exit;

