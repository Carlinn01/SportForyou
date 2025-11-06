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
    header("Location: ../pages/admin_stories.php");
    exit;
}

// Validação de ID - converte para int e valida
$idstory = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($idstory <= 0) {
    $_SESSION['msg'] = 'ID do story inválido.';
    $_SESSION['msg_tipo'] = 'erro';
    header("Location: ../pages/admin_stories.php");
    exit;
}

try {
    // Busca informações do story
    $sql = "SELECT midia FROM stories WHERE idstory = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$idstory]);
    $storyInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$storyInfo) {
        $_SESSION['msg'] = 'Story não encontrado.';
        $_SESSION['msg_tipo'] = 'erro';
        header("Location: ../pages/admin_stories.php");
        exit;
    }
    
    // Deleta o story
    $sql = "DELETE FROM stories WHERE idstory = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$idstory]);
    
    // Remove arquivo de mídia
    if ($storyInfo['midia']) {
        $caminhoMidia = '../login/' . $storyInfo['midia'];
        if (file_exists($caminhoMidia)) {
            unlink($caminhoMidia);
        }
    }
    
    $_SESSION['msg'] = 'Story deletado com sucesso!';
    $_SESSION['msg_tipo'] = 'sucesso';
    
} catch (PDOException $e) {
    error_log("Erro ao deletar story: " . $e->getMessage());
    $_SESSION['msg'] = 'Erro ao deletar story. Tente novamente.';
    $_SESSION['msg_tipo'] = 'erro';
}

header("Location: ../pages/admin_stories.php");
exit;

