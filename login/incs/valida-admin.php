<?php
/**
 * Validação de Administrador
 * Este arquivo verifica se o usuário logado é um administrador
 * Use este arquivo em todas as páginas e ações administrativas
 */

include("valida-sessao.php");
require_once __DIR__ . "/../src/ConexaoBD.php";

// Verifica se o usuário está logado
if (!isset($_SESSION['idusuarios'])) {
    header("Location: ../login/login.php");
    exit;
}

$idusuario_logado = $_SESSION['idusuarios'];
$conexao = ConexaoBD::conectar();

// Função para verificar se uma coluna existe na tabela (se não existir, declara)
if (!function_exists('colunaExiste')) {
    function colunaExiste($pdo, $tabela, $coluna) {
        try {
            $sql = "SHOW COLUMNS FROM $tabela LIKE '$coluna'";
            $stmt = $pdo->query($sql);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
}

// Cria a coluna is_admin se não existir
if (!colunaExiste($conexao, 'usuarios', 'is_admin')) {
    try {
        $conexao->exec("ALTER TABLE usuarios ADD COLUMN is_admin TINYINT(1) DEFAULT 0 NOT NULL");
    } catch (PDOException $e) {
        error_log("Erro ao criar coluna is_admin: " . $e->getMessage());
    }
}

// Verifica se o usuário é admin
try {
    $sql = "SELECT is_admin FROM usuarios WHERE idusuarios = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$idusuario_logado]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$resultado || !$resultado['is_admin']) {
        // Usuário não é admin - redireciona para home
        header("Location: ../../pages/home.php");
        exit;
    }
} catch (PDOException $e) {
    error_log("Erro ao verificar admin: " . $e->getMessage());
    header("Location: ../../pages/home.php");
    exit;
}

// Define variável global para uso em outras páginas
$_SESSION['is_admin'] = true;

