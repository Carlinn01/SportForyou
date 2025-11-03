<?php
include "../login/incs/valida-sessao.php";
require_once "../login/src/UsuarioDAO.php";

header('Content-Type: application/json');

$idusuario_logado = $_SESSION['idusuarios'];
$notificacoes = UsuarioDAO::listarNotificacoes($idusuario_logado);

echo json_encode([
    'success' => true,
    'total' => count($notificacoes)
]);

