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

$seguindo = SeguidoDAO::listarSeguindo($usuario_id);

// Verifica se o usuário logado está seguindo cada pessoa
$conexao = ConexaoBD::conectar();
foreach ($seguindo as &$usuario) {
    $sqlVerifica = "SELECT COUNT(*) FROM seguidores WHERE idseguidor = ? AND idusuario = ?";
    $stmtVerifica = $conexao->prepare($sqlVerifica);
    $stmtVerifica->execute([$idusuario_logado, $usuario['idusuarios']]);
    $usuario['ja_sigo'] = $stmtVerifica->fetchColumn() > 0;
    $usuario['eh_eu'] = ($usuario['idusuarios'] == $idusuario_logado);
}

echo json_encode($seguindo);
?>

