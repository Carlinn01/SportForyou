<?php
include("../login/incs/valida-sessao.php");
require_once "../login/src/ConexaoBD.php";

header('Content-Type: application/json');

$idusuario_logado = $_SESSION['idusuarios'];
$conexao = ConexaoBD::conectar();

$idmensagem = $_GET['id'] ?? null;

if (!$idmensagem || !is_numeric($idmensagem)) {
    echo json_encode(['success' => false]);
    exit;
}

$idmensagem = (int)$idmensagem;

// Busca status da mensagem
$sql = "SELECT status FROM mensagens WHERE idmensagem = ?";
$stmt = $conexao->prepare($sql);
$stmt->execute([$idmensagem]);
$status = $stmt->fetchColumn();

echo json_encode([
    'success' => true,
    'status' => $status ?: 'enviada'
]);
exit;

