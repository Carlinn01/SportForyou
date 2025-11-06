<?php
include("../login/incs/valida-admin.php");
require_once "../login/src/ConexaoBD.php";
require_once "../login/src/CSRF.php";

$idusuario_logado = $_SESSION['idusuarios'];
$conexao = ConexaoBD::conectar();

// Validação CSRF
$token = $_GET['token'] ?? '';
if (!CSRF::validarToken($token)) {
    $_SESSION['msg'] = 'Token de segurança inválido.';
    $_SESSION['msg_tipo'] = 'erro';
    header("Location: ../pages/admin_posts.php");
    exit;
}

// Validação de ID - converte para int e valida
$idpostagem = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($idpostagem <= 0) {
    $_SESSION['msg'] = 'ID da postagem inválido.';
    $_SESSION['msg_tipo'] = 'erro';
    header("Location: ../pages/admin_posts.php");
    exit;
}

try {
    // Busca informações da postagem
    $sql = "SELECT foto FROM postagens WHERE idpostagem = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$idpostagem]);
    $postInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$postInfo) {
        $_SESSION['msg'] = 'Postagem não encontrada.';
        $_SESSION['msg_tipo'] = 'erro';
        header("Location: ../pages/admin_posts.php");
        exit;
    }
    
    // Deleta curtidas
    $sql = "DELETE FROM curtidas WHERE idpostagem = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$idpostagem]);
    
    // Deleta comentários
    $sql = "DELETE FROM comentarios WHERE idpostagem = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$idpostagem]);
    
    // Deleta a postagem
    $sql = "DELETE FROM postagens WHERE idpostagem = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$idpostagem]);
    
    // Remove arquivo de foto
    if ($postInfo['foto']) {
        $caminhoFoto = '../login/uploads/' . $postInfo['foto'];
        if (file_exists($caminhoFoto)) {
            unlink($caminhoFoto);
        }
    }
    
    $_SESSION['msg'] = 'Post deletado com sucesso!';
    $_SESSION['msg_tipo'] = 'sucesso';
    
} catch (PDOException $e) {
    error_log("Erro ao deletar post: " . $e->getMessage());
    $_SESSION['msg'] = 'Erro ao deletar post. Tente novamente.';
    $_SESSION['msg_tipo'] = 'erro';
}

header("Location: ../pages/admin_posts.php");
exit;

