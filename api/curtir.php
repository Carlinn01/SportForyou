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

// Verifica se o usuário já curtiu
$stmt = $pdo->prepare("SELECT * FROM curtidas WHERE idusuario = ? AND idpostagem = ?");
$stmt->execute([$idusuario, $idpostagem]);

$jaCurtiu = $stmt->rowCount() > 0;

if ($jaCurtiu) {
    // Se já curtiu, removemos a curtida
    $pdo->prepare("DELETE FROM curtidas WHERE idusuario = ? AND idpostagem = ?")
         ->execute([$idusuario, $idpostagem]);
    $action = 'unliked';
} else {
    // Se não curtiu, adicionamos a curtida
    $pdo->prepare("INSERT INTO curtidas (idusuario, idpostagem) VALUES (?, ?)")
         ->execute([$idusuario, $idpostagem]);
    $action = 'liked';
}

// Conta as curtidas atualizadas
$stmt = $pdo->prepare("SELECT COUNT(*) FROM curtidas WHERE idpostagem = ?");
$stmt->execute([$idpostagem]);
$totalCurtidas = $stmt->fetchColumn();

echo json_encode([
    'success' => true,
    'action' => $action,
    'curtidas' => (int)$totalCurtidas
]);
exit;
?>
