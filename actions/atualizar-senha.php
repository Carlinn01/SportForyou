<?php
require_once "../login/src/ConexaoBD.php";

$token = $_POST['token'] ?? '';
$nova_senha = $_POST['nova_senha'] ?? '';
$confirmar_senha = $_POST['confirmar_senha'] ?? '';

if (empty($token) || empty($nova_senha) || empty($confirmar_senha)) {
    header("Location: ../pages/redefinir-senha.php?token=$token&erro=" . urlencode("Preencha todos os campos."));
    exit;
}

if (strlen($nova_senha) < 6) {
    header("Location: ../pages/redefinir-senha.php?token=$token&erro=" . urlencode("A senha deve ter pelo menos 6 caracteres."));
    exit;
}

if ($nova_senha !== $confirmar_senha) {
    header("Location: ../pages/redefinir-senha.php?token=$token&erro=" . urlencode("As senhas não coincidem."));
    exit;
}

try {
    $pdo = ConexaoBD::conectar();
    
    // Verifica se o token é válido e não expirou
    $sql = $pdo->prepare("SELECT idusuarios FROM usuarios WHERE token_recuperacao = ? AND token_expira > NOW()");
    $sql->execute([$token]);
    
    if ($sql->rowCount() == 0) {
        header("Location: ../pages/redefinir-senha.php?token=$token&erro=" . urlencode("Token inválido ou expirado!"));
        exit;
    }
    
    $usuario = $sql->fetch(PDO::FETCH_ASSOC);
    $idusuario = $usuario['idusuarios'];
    
    // Hash da senha usando MD5 (mesmo método usado no sistema)
    $hash = md5($nova_senha);
    
    // Atualiza a senha e limpa o token
    $sqlUpdate = $pdo->prepare("UPDATE usuarios SET senha = ?, token_recuperacao = NULL, token_expira = NULL WHERE idusuarios = ?");
    $sqlUpdate->execute([$hash, $idusuario]);
    
    header("Location: ../login/login.php?sucesso=" . urlencode("Senha redefinida com sucesso! Você já pode fazer login."));
    exit;
    
} catch (PDOException $e) {
    error_log("Erro ao atualizar senha: " . $e->getMessage());
    header("Location: ../pages/redefinir-senha.php?token=$token&erro=" . urlencode("Erro ao atualizar senha. Tente novamente."));
    exit;
}

