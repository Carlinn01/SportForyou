<?php
include("../login/incs/valida-sessao.php");
require_once "../login/src/ConexaoBD.php";

header('Content-Type: application/json');

$idusuario_logado = $_SESSION['idusuarios'];
$conexao = ConexaoBD::conectar();

$conversa_id = $_GET['conversa_id'] ?? null;
$ultima_mensagem_id = $_GET['ultima_mensagem_id'] ?? 0;

if (!$conversa_id || !is_numeric($conversa_id)) {
    echo json_encode(['success' => false, 'message' => 'ID de conversa inválido']);
    exit;
}

$conversa_id = (int)$conversa_id;

// Verifica permissão
$sqlVerifica = "SELECT idconversa FROM conversas WHERE idconversa = ? AND (usuario1_id = ? OR usuario2_id = ?)";
$stmt = $conexao->prepare($sqlVerifica);
$stmt->execute([$conversa_id, $idusuario_logado, $idusuario_logado]);
if (!$stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Conversa não encontrada']);
    exit;
}

// Busca novas mensagens
$sql = "SELECT m.*, u.nome, u.nome_usuario, u.foto_perfil, m.anexo_url
        FROM mensagens m
        JOIN usuarios u ON m.remetente_id = u.idusuarios
        WHERE m.conversa_id = ? AND m.idmensagem > ?
        ORDER BY m.criado_em ASC";
$stmt = $conexao->prepare($sql);
$stmt->execute([$conversa_id, $ultima_mensagem_id]);
$mensagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Marca mensagens como lidas
if (!empty($mensagens)) {
    $idsMensagens = array_column($mensagens, 'idmensagem');
    $placeholders = implode(',', array_fill(0, count($idsMensagens), '?'));
    
    // Insere registros de mensagens lidas
    $sqlLidas = "INSERT IGNORE INTO mensagens_lidas (mensagem_id, usuario_id) 
                 SELECT idmensagem, ? FROM mensagens 
                 WHERE idmensagem IN ($placeholders) AND remetente_id != ?";
    $stmt = $conexao->prepare($sqlLidas);
    $params = array_merge([$idusuario_logado], $idsMensagens, [$idusuario_logado]);
    $stmt->execute($params);
    
    // Atualiza status das mensagens recebidas como "lida"
    $sqlUpdate = "UPDATE mensagens SET status = 'lida' 
                  WHERE idmensagem IN ($placeholders) AND remetente_id != ?";
    $stmt = $conexao->prepare($sqlUpdate);
    $params2 = array_merge($idsMensagens, [$idusuario_logado]);
    $stmt->execute($params2);
    
    // Busca novamente as mensagens para ter o status atualizado
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$conversa_id, $ultima_mensagem_id]);
    $mensagens = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Formata mensagens para retorno
$resultado = [];
foreach ($mensagens as $msg) {
    $eh_minha = $msg['remetente_id'] == $idusuario_logado;
    $resultado[] = [
        'id' => $msg['idmensagem'],
        'conteudo' => $msg['conteudo'],
        'remetente_id' => $msg['remetente_id'],
        'nome' => $msg['nome'],
        'nome_usuario' => $msg['nome_usuario'],
        'foto_perfil' => $msg['foto_perfil'],
        'status' => $msg['status'],
        'hora' => date('H:i', strtotime($msg['criado_em'])),
        'eh_minha' => $eh_minha
    ];
}

echo json_encode([
    'success' => true,
    'mensagens' => $resultado
]);
exit;

