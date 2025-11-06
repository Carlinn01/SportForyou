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

$erro_id = $_POST['erro_id'] ?? null;
$novo_status = $_POST['novo_status'] ?? null;

if (!$erro_id || !$novo_status) {
    $_SESSION['msg'] = 'Dados inválidos.';
    $_SESSION['msg_tipo'] = 'erro';
    header("Location: ../pages/ver_erros.php");
    exit;
}

// Valida o status
$statusValidos = ['pendente', 'em_analise', 'resolvido', 'descartado'];
if (!in_array($novo_status, $statusValidos)) {
    $_SESSION['msg'] = 'Status inválido.';
    $_SESSION['msg_tipo'] = 'erro';
    header("Location: ../pages/ver_erros.php");
    exit;
}

// Atualiza o status do erro
try {
    $sql = "UPDATE erros_reportados SET status = ? WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$novo_status, $erro_id]);
    
    $_SESSION['msg'] = 'Status atualizado com sucesso!';
    $_SESSION['msg_tipo'] = 'sucesso';
    
} catch (PDOException $e) {
    error_log("Erro ao atualizar status: " . $e->getMessage());
    $_SESSION['msg'] = 'Erro ao atualizar status. Tente novamente.';
    $_SESSION['msg_tipo'] = 'erro';
}

header("Location: ../pages/ver_erros.php");
exit;

