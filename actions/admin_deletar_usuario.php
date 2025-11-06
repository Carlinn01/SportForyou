<?php
include("../login/incs/valida-admin.php");
require_once "../login/src/ConexaoBD.php";

$idusuario_logado = $_SESSION['idusuarios'];
$conexao = ConexaoBD::conectar();

$idusuario_deletar = $_GET['id'] ?? null;

if (!$idusuario_deletar) {
    $_SESSION['msg'] = 'ID do usuário não informado.';
    $_SESSION['msg_tipo'] = 'erro';
    header("Location: ../pages/admin_usuarios.php");
    exit;
}

// Não permite deletar a si mesmo
if ($idusuario_deletar == $idusuario_logado) {
    $_SESSION['msg'] = 'Você não pode deletar sua própria conta.';
    $_SESSION['msg_tipo'] = 'erro';
    header("Location: ../pages/admin_usuarios.php");
    exit;
}

try {
    $conexao->beginTransaction();
    
    // Busca foto de perfil do usuário
    $sqlFoto = "SELECT foto_perfil FROM usuarios WHERE idusuarios = ?";
    $stmtFoto = $conexao->prepare($sqlFoto);
    $stmtFoto->execute([$idusuario_deletar]);
    $fotoPerfil = $stmtFoto->fetchColumn();
    
    // Deleta curtidas dos posts do usuário
    $sql = "DELETE c FROM curtidas c 
            INNER JOIN postagens p ON c.idpostagem = p.idpostagem 
            WHERE p.idusuario = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$idusuario_deletar]);
    
    // Deleta comentários dos posts do usuário
    $sql = "DELETE co FROM comentarios co 
            INNER JOIN postagens p ON co.idpostagem = p.idpostagem 
            WHERE p.idusuario = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$idusuario_deletar]);
    
    // Deleta comentários do usuário
    $sql = "DELETE FROM comentarios WHERE idusuario = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$idusuario_deletar]);
    
    // Deleta curtidas do usuário
    $sql = "DELETE FROM curtidas WHERE idusuario = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$idusuario_deletar]);
    
    // Busca fotos dos posts do usuário
    $sql = "SELECT foto FROM postagens WHERE idusuario = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$idusuario_deletar]);
    $fotosPosts = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Deleta posts do usuário
    $sql = "DELETE FROM postagens WHERE idusuario = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$idusuario_deletar]);
    
    // Busca mídias dos stories do usuário
    $sql = "SELECT midia FROM stories WHERE idusuario = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$idusuario_deletar]);
    $midiasStories = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Deleta stories do usuário
    $sql = "DELETE FROM stories WHERE idusuario = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$idusuario_deletar]);
    
    // Deleta seguidores (onde o usuário segue)
    $sql = "DELETE FROM seguidores WHERE idseguidor = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$idusuario_deletar]);
    
    // Deleta seguidores (onde seguem o usuário)
    $sql = "DELETE FROM seguidores WHERE idusuario = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$idusuario_deletar]);
    
    // Deleta o usuário
    $sql = "DELETE FROM usuarios WHERE idusuarios = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$idusuario_deletar]);
    
    $conexao->commit();
    
    // Remove arquivos
    $pasta = '../login/uploads/';
    
    if ($fotoPerfil && file_exists($pasta . $fotoPerfil)) {
        unlink($pasta . $fotoPerfil);
    }
    
    foreach ($fotosPosts as $foto) {
        if ($foto && file_exists($pasta . $foto)) {
            unlink($pasta . $foto);
        }
    }
    
    foreach ($midiasStories as $midia) {
        $caminhoCompleto = '../login/' . $midia;
        if ($midia && file_exists($caminhoCompleto)) {
            unlink($caminhoCompleto);
        }
    }
    
    $_SESSION['msg'] = 'Usuário deletado com sucesso!';
    $_SESSION['msg_tipo'] = 'sucesso';
    
} catch (PDOException $e) {
    $conexao->rollBack();
    error_log("Erro ao deletar usuário: " . $e->getMessage());
    $_SESSION['msg'] = 'Erro ao deletar usuário. Tente novamente.';
    $_SESSION['msg_tipo'] = 'erro';
}

header("Location: ../pages/admin_usuarios.php");
exit;

