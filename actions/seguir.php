<?php
include("../login/incs/valida-sessao.php");
require_once "../login/src/SeguidoDAO.php";
require_once "../login/src/UsuarioDAO.php"; // Para adicionar a notificação

if (isset($_GET["idseguidor"])) {
    $idseguidor = $_SESSION["idusuarios"];  // Quem está seguindo
    $idusuario = $_GET["idseguidor"];       // Quem será seguido

    // Adiciona o registro de seguidor
    // A função seguir espera: seguir($idusuario_seguido, $idseguidor_seguindo)
    SeguidoDAO::seguir($idusuario, $idseguidor);

    // Cria mensagem de notificação
    $mensagem = "@" . $_SESSION['nome_usuario'] . " começou a seguir você!";

    // Adiciona a notificação para o usuário seguido
    UsuarioDAO::adicionarNotificacao($idusuario, 'seguidor', $mensagem);
} 

// Redireciona de volta para a página de origem (ou home.php se não houver referer)
$referer = $_SERVER['HTTP_REFERER'] ?? '../pages/home.php';
header("Location: " . $referer);
exit();
