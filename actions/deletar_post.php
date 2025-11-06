<?php
include("../login/incs/valida-sessao.php");
require_once "../login/src/ConexaoBD.php";
require_once "../login/src/CSRF.php";

$idusuario = $_SESSION['idusuarios'];
$conexao = ConexaoBD::conectar();

// Validação CSRF
$token = $_GET['token'] ?? '';
if (!CSRF::validarToken($token)) {
    $_SESSION['msg'] = 'Token de segurança inválido.';
    $_SESSION['msg_tipo'] = 'erro';
    header("Location: ../pages/perfil.php?id=" . $idusuario);
    exit;
}

// Validação de ID - converte para int e valida
$idpostagem = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($idpostagem <= 0) {
    $_SESSION['msg'] = 'ID do post inválido.';
    $_SESSION['msg_tipo'] = 'erro';
    header("Location: ../pages/perfil.php?id=" . $idusuario);
    exit;
}

try {
    // Usa transação para garantir atomicidade e evitar race conditions
    $conexao->beginTransaction();
    
    // Verifica se o post existe e se pertence ao usuário (dentro da transação)
    $sqlVerificar = "SELECT idpostagem, idusuario, foto FROM postagens WHERE idpostagem = ? AND idusuario = ? FOR UPDATE";
    $stmtVerificar = $conexao->prepare($sqlVerificar);
    $stmtVerificar->execute([$idpostagem, $idusuario]);
    $post = $stmtVerificar->fetch(PDO::FETCH_ASSOC);
    
    if (!$post) {
        $conexao->rollBack();
        $_SESSION['msg'] = 'Post não encontrado ou você não tem permissão para deletá-lo.';
        $_SESSION['msg_tipo'] = 'erro';
        header("Location: ../pages/perfil.php?id=" . $idusuario);
        exit;
    }
    
    // Deleta curtidas relacionadas
    $sqlCurtidas = "DELETE FROM curtidas WHERE idpostagem = ?";
    $stmtCurtidas = $conexao->prepare($sqlCurtidas);
    $stmtCurtidas->execute([$idpostagem]);
    
    // Deleta comentários relacionados
    $sqlComentarios = "DELETE FROM comentarios WHERE idpostagem = ?";
    $stmtComentarios = $conexao->prepare($sqlComentarios);
    $stmtComentarios->execute([$idpostagem]);
    
    // Deleta o post
    $sqlDeletar = "DELETE FROM postagens WHERE idpostagem = ?";
    $stmtDeletar = $conexao->prepare($sqlDeletar);
    $stmtDeletar->execute([$idpostagem]);
    
    $conexao->commit();
    
    // Remove a foto do servidor se existir (após commit bem-sucedido)
    if ($post['foto']) {
        $caminhoFoto = '../login/uploads/' . basename($post['foto']); // Previne path traversal
        if (file_exists($caminhoFoto) && strpos(realpath($caminhoFoto), realpath('../login/uploads/')) === 0) {
            unlink($caminhoFoto);
        }
    }
    
    $_SESSION['msg'] = 'Post deletado com sucesso!';
    $_SESSION['msg_tipo'] = 'sucesso';
    
} catch (PDOException $e) {
    if ($conexao->inTransaction()) {
        $conexao->rollBack();
    }
    error_log("Erro ao deletar post: " . $e->getMessage());
    $_SESSION['msg'] = 'Erro ao deletar post. Tente novamente.';
    $_SESSION['msg_tipo'] = 'erro';
}

header("Location: ../pages/perfil.php?id=" . $idusuario);
exit;

