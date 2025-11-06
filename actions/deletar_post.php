<?php
include("../login/incs/valida-sessao.php");
require_once "../login/src/ConexaoBD.php";

$idusuario = $_SESSION['idusuarios'];
$idpostagem = $_GET['id'] ?? null;

if (!$idpostagem) {
    $_SESSION['msg'] = 'ID do post não informado.';
    $_SESSION['msg_tipo'] = 'erro';
    header("Location: ../pages/perfil.php?id=" . $idusuario);
    exit;
}

$conexao = ConexaoBD::conectar();

try {
    // Verifica se o post existe e se pertence ao usuário
    $sqlVerificar = "SELECT idpostagem, idusuario, foto FROM postagens WHERE idpostagem = ? AND idusuario = ?";
    $stmtVerificar = $conexao->prepare($sqlVerificar);
    $stmtVerificar->execute([$idpostagem, $idusuario]);
    $post = $stmtVerificar->fetch(PDO::FETCH_ASSOC);
    
    if (!$post) {
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
    
    // Remove a foto do servidor se existir
    if ($post['foto'] && file_exists('../login/uploads/' . $post['foto'])) {
        unlink('../login/uploads/' . $post['foto']);
    }
    
    $_SESSION['msg'] = 'Post deletado com sucesso!';
    $_SESSION['msg_tipo'] = 'sucesso';
    
} catch (PDOException $e) {
    error_log("Erro ao deletar post: " . $e->getMessage());
    $_SESSION['msg'] = 'Erro ao deletar post. Tente novamente.';
    $_SESSION['msg_tipo'] = 'erro';
}

header("Location: ../pages/perfil.php?id=" . $idusuario);
exit;

