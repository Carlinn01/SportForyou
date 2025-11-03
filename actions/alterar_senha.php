<?php
include("../login/incs/valida-sessao.php");
require_once "../login/src/ConexaoBD.php";
require_once "../login/src/UsuarioDAO.php";

$idusuario_logado = $_SESSION['idusuarios'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pages/configuracoes.php");
    exit;
}

$nova_senha = trim($_POST['nova_senha'] ?? '');
$confirmar_senha = trim($_POST['confirmar_senha'] ?? '');

// Validações
if (empty($nova_senha) || empty($confirmar_senha)) {
    header("Location: configuracoes.php?erro=" . urlencode("Preencha todos os campos"));
    exit;
}

if (strlen($nova_senha) < 6) {
    header("Location: configuracoes.php?erro=" . urlencode("A senha deve ter pelo menos 6 caracteres"));
    exit;
}

if ($nova_senha !== $confirmar_senha) {
    header("Location: configuracoes.php?erro=" . urlencode("As senhas não coincidem"));
    exit;
}

// Atualiza a senha no banco de dados
try {
    $conexao = ConexaoBD::conectar();
    
    // Hash da senha usando MD5 (mesmo método usado no sistema atual)
    // NOTA: Idealmente, deveria usar password_hash() para segurança maior
    $senha_hash = md5($nova_senha);
    
    $sql = "UPDATE usuarios SET senha = ? WHERE idusuarios = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$senha_hash, $idusuario_logado]);
    
    if ($stmt->rowCount() > 0) {
        header("Location: configuracoes.php?mensagem=" . urlencode("Senha alterada com sucesso!"));
    } else {
        header("Location: configuracoes.php?erro=" . urlencode("Erro ao alterar senha. Tente novamente."));
    }
} catch (PDOException $e) {
    error_log("Erro ao alterar senha: " . $e->getMessage());
    header("Location: configuracoes.php?erro=" . urlencode("Erro ao alterar senha. Tente novamente."));
}
exit;

