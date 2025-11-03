<?php
include "../login/incs/valida-sessao.php";
require_once "../login/src/SeguidoDAO.php";
require_once "../login/src/ConexaoBD.php";

header('Content-Type: application/json');

if (!isset($_GET['usuario_id'])) {
    echo json_encode(['erro' => 'ID do usuário não fornecido']);
    exit;
}

$usuario_id = intval($_GET['usuario_id']);
$idusuario_logado = $_SESSION['idusuarios'];

$seguidores = SeguidoDAO::listarSeguidores($usuario_id);

// Verifica se o usuário logado está seguindo cada seguidor
$conexao = ConexaoBD::conectar();
foreach ($seguidores as &$seguidor) {
    $sqlVerifica = "SELECT COUNT(*) FROM seguidores WHERE idseguidor = ? AND idusuario = ?";
    $stmtVerifica = $conexao->prepare($sqlVerifica);
    $stmtVerifica->execute([$idusuario_logado, $seguidor['idusuarios']]);
    $seguidor['ja_sigo'] = $stmtVerifica->fetchColumn() > 0;
    $seguidor['eh_eu'] = ($seguidor['idusuarios'] == $idusuario_logado);
}

echo json_encode($seguidores);
?>

