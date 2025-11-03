<?php
include "../login/incs/valida-sessao.php";
require_once "../login/src/UsuarioDAO.php";

$idusuario_logado = $_SESSION['idusuarios'];
UsuarioDAO::limparNotificacoes($idusuario_logado);

// Redireciona de volta para a página de origem
$referer = $_SERVER['HTTP_REFERER'] ?? '../pages/home.php';
header("Location: " . $referer);
exit;

