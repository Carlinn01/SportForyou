<?php
include("login/incs/valida-sessao.php");
require_once "login/src/ConexaoBD.php";

header('Content-Type: application/json');

$idusuario_logado = $_SESSION['idusuarios'];
$conexao = ConexaoBD::conectar();

$conversa_id = $_POST['conversa_id'] ?? null;
$mensagem = trim($_POST['mensagem'] ?? '');

if (!$conversa_id || !is_numeric($conversa_id)) {
    echo json_encode(['success' => false, 'message' => 'ID de conversa inválido']);
    exit;
}

if (empty($mensagem)) {
    echo json_encode(['success' => false, 'message' => 'Mensagem não pode estar vazia']);
    exit;
}

$conversa_id = (int)$conversa_id;

// Verifica se o usuário tem permissão nesta conversa
$sqlVerifica = "SELECT idconversa FROM conversas WHERE idconversa = ? AND (usuario1_id = ? OR usuario2_id = ?)";
$stmt = $conexao->prepare($sqlVerifica);
$stmt->execute([$conversa_id, $idusuario_logado, $idusuario_logado]);
if (!$stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Conversa não encontrada ou sem permissão']);
    exit;
}

// Insere a mensagem
$sql = "INSERT INTO mensagens (conversa_id, remetente_id, conteudo, status) VALUES (?, ?, ?, 'enviada')";
$stmt = $conexao->prepare($sql);
$stmt->execute([$conversa_id, $idusuario_logado, $mensagem]);

$idmensagem = $conexao->lastInsertId();

// Atualiza a última mensagem da conversa
$sqlUpdate = "UPDATE conversas SET ultima_mensagem_id = ?, atualizado_em = NOW() WHERE idconversa = ?";
$stmt = $conexao->prepare($sqlUpdate);
$stmt->execute([$idmensagem, $conversa_id]);

// Marca como entregue (atualiza status)
$sqlStatus = "UPDATE mensagens SET status = 'entregue' WHERE idmensagem = ?";
$stmt = $conexao->prepare($sqlStatus);
$stmt->execute([$idmensagem]);

// Busca dados do usuário para retornar
$sqlUsuario = "SELECT nome, nome_usuario, foto_perfil FROM usuarios WHERE idusuarios = ?";
$stmt = $conexao->prepare($sqlUsuario);
$stmt->execute([$idusuario_logado]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'mensagem' => [
        'id' => $idmensagem,
        'conteudo' => htmlspecialchars($mensagem),
        'remetente_id' => $idusuario_logado,
        'nome' => $usuario['nome'],
        'nome_usuario' => $usuario['nome_usuario'],
        'foto_perfil' => $usuario['foto_perfil'],
        'status' => 'entregue',
        'hora' => date('H:i'),
        'data' => date('Y-m-d H:i:s')
    ]
]);
exit;

