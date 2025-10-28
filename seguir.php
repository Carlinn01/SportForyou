<?php
include("login/incs/valida-sessao.php");
require_once "login/src/SeguidoDAO.php";
require_once "login/src/UsuarioDAO.php"; // Para adicionar a notificação

if (isset($_GET["idseguidor"])) {
    $idseguidor = $_SESSION["idusuarios"];  // Quem está seguindo
    $idusuario = $_GET["idseguidor"];       // Quem será seguido

    // Adiciona o registro de seguidor
    SeguidoDAO::seguir($idseguidor, $idusuario);

    // Cria mensagem de notificação
    $mensagem = "@" . $_SESSION['nome_usuario'] . " começou a seguir você!";

    // Adiciona a notificação para o usuário seguido
    UsuarioDAO::adicionarNotificacao($idusuario, 'seguidor', $mensagem);
} 

// Redireciona para a página de seguidores
header("location:seguidores.php");
exit();
