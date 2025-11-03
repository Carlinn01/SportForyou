<?php
include("../login/incs/valida-sessao.php");
require_once "../login/src/UsuarioDAO.php";

header('Content-Type: application/json');

$id_notificacao = $_POST['id'] ?? $_GET['id'] ?? null;

if (!$id_notificacao || !is_numeric($id_notificacao)) {
    echo json_encode(['success' => false, 'message' => 'ID de notificação inválido']);
    exit;
}

$id_notificacao = (int)$id_notificacao;
$idusuario_logado = $_SESSION['idusuarios'];

// Verifica se a notificação pertence ao usuário logado
$conexao = ConexaoBD::conectar();
$sqlVerifica = "SELECT id FROM notificacoes WHERE id = ? AND id_usuario = ?";
$stmt = $conexao->prepare($sqlVerifica);
$stmt->execute([$id_notificacao, $idusuario_logado]);

if (!$stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Notificação não encontrada']);
    exit;
}

// Marca como lida
UsuarioDAO::marcarComoLida($id_notificacao);

echo json_encode(['success' => true]);
exit;

