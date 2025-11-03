<?php
include "../login/incs/valida-sessao.php";
require_once "../login/src/ConexaoBD.php";

// Define header JSON
header('Content-Type: application/json');

$idusuario = $_SESSION['idusuarios'];
$idpostagem = $_GET['idpostagem'] ?? $_POST['idpostagem'] ?? null;

if (!$idpostagem || !is_numeric($idpostagem)) {
    echo json_encode(['success' => false, 'message' => 'ID de postagem inválido']);
    exit;
}

$idpostagem = (int)$idpostagem;
$pdo = ConexaoBD::conectar();

// Insere o compartilhamento no banco de dados
$sql = "INSERT INTO compartilhamentos (idusuario, idpostagem, data_compartilhamento) VALUES (?, ?, NOW())";
$stmt = $pdo->prepare($sql);
$stmt->execute([$idusuario, $idpostagem]);

// Conta os compartilhamentos atualizados
$stmt = $pdo->prepare("SELECT COUNT(*) FROM compartilhamentos WHERE idpostagem = ?");
$stmt->execute([$idpostagem]);
$totalCompartilhamentos = $stmt->fetchColumn();

echo json_encode([
    'success' => true,
    'compartilhamentos' => (int)$totalCompartilhamentos
]);
exit;

