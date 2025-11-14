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
    header("Location: ../pages/admin_usuarios.php");
    exit;
}

// Validação de ID - converte para int e valida
$idusuario_deletar = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($idusuario_deletar <= 0) {
    $_SESSION['msg'] = 'ID do usuário inválido.';
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
    
    // Deleta notificações do usuário
    $sql = "DELETE FROM notificacoes WHERE idusuario = ? OR id_usuario = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$idusuario_deletar, $idusuario_deletar]);
    
    // Define pasta de uploads para uso posterior
    $pasta = '../login/uploads/';
    
    // Busca fotos dos eventos criados pelo usuário antes de deletar
    $sql = "SELECT foto FROM eventos WHERE organizador_id = ? AND foto IS NOT NULL AND foto != ''";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$idusuario_deletar]);
    $fotosEventos = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Busca IDs dos eventos criados pelo usuário
    $sql = "SELECT idevento FROM eventos WHERE organizador_id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$idusuario_deletar]);
    $eventosIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Deleta interessados nos eventos (se houver eventos)
    if (!empty($eventosIds)) {
        $placeholders = str_repeat('?,', count($eventosIds) - 1) . '?';
        $sql = "DELETE FROM eventos_interessados WHERE evento_id IN ($placeholders)";
        $stmt = $conexao->prepare($sql);
        $stmt->execute($eventosIds);
    }
    
    // Deleta eventos do usuário
    $sql = "DELETE FROM eventos WHERE organizador_id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$idusuario_deletar]);
    
    // Remove fotos dos eventos
    foreach ($fotosEventos as $foto) {
        if ($foto && file_exists($pasta . $foto)) {
            unlink($pasta . $foto);
        }
    }
    
    // Primeiro, deleta mensagens lidas do usuário
    $sql = "DELETE ml FROM mensagens_lidas ml
            INNER JOIN mensagens m ON ml.mensagem_id = m.idmensagem
            WHERE m.remetente_id = ? OR ml.usuario_id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$idusuario_deletar, $idusuario_deletar]);
    
    // Busca IDs das conversas do usuário antes de deletar
    $sql = "SELECT idconversa FROM conversas WHERE usuario1_id = ? OR usuario2_id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$idusuario_deletar, $idusuario_deletar]);
    $conversasIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Deleta mensagens das conversas do usuário
    if (!empty($conversasIds)) {
        $placeholders = str_repeat('?,', count($conversasIds) - 1) . '?';
        $sql = "DELETE FROM mensagens WHERE conversa_id IN ($placeholders)";
        $stmt = $conexao->prepare($sql);
        $stmt->execute($conversasIds);
    }
    
    // Deleta conversas do usuário (isso também deleta as mensagens por cascade, mas já deletamos acima)
    $sql = "DELETE FROM conversas WHERE usuario1_id = ? OR usuario2_id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$idusuario_deletar, $idusuario_deletar]);
    
    // Deleta o usuário
    $sql = "DELETE FROM usuarios WHERE idusuarios = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$idusuario_deletar]);
    
    $conexao->commit();
    
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

