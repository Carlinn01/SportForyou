<?php
include "../login/incs/valida-sessao.php";
require_once "../login/src/ConexaoBD.php";

header('Content-Type: application/json');

$idusuario_logado = $_SESSION['idusuarios'];
$conexao = ConexaoBD::conectar();

// Conta mensagens não lidas de todas as conversas
$sql = "SELECT COUNT(*) 
        FROM mensagens m
        LEFT JOIN mensagens_lidas ml ON m.idmensagem = ml.mensagem_id AND ml.usuario_id = ?
        WHERE m.remetente_id != ?
        AND ml.id IS NULL";

$stmt = $conexao->prepare($sql);
$stmt->execute([$idusuario_logado, $idusuario_logado]);
$totalMensagensNaoLidas = $stmt->fetchColumn();

echo json_encode([
    'success' => true,
    'total' => (int)$totalMensagensNaoLidas
]);

