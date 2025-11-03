<?php
include "../login/incs/valida-sessao.php";
require_once "../login/src/StoryDAO.php";

header('Content-Type: application/json');

$idusuario_logado = $_SESSION['idusuarios'];
$idstory = $_POST['idstory'] ?? null;

if (!$idstory || !is_numeric($idstory)) {
    echo json_encode(['success' => false, 'message' => 'ID de story inválido']);
    exit;
}

$idstory = (int)$idstory;

// Deleta usando o DAO
if (StoryDAO::deletar($idstory, $idusuario_logado)) {
    echo json_encode(['success' => true, 'message' => 'Story deletado com sucesso']);
} else {
    echo json_encode(['success' => false, 'message' => 'Story não encontrado ou sem permissão']);
}

