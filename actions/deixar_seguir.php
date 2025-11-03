<?php
include("../login/incs/valida-sessao.php");
require_once "../login/src/SeguidoDAO.php";

if (isset($_GET["idseguidor"])) {
    // A função deixarDeSeguir espera: deixarDeSeguir($idusuario_seguido, $idseguidor_seguindo)
    // $_GET["idseguidor"] = quem é seguido (idusuario na tabela)
    // $_SESSION["idusuarios"] = quem está seguindo (idseguidor na tabela)
    SeguidoDAO::deixarDeSeguir($_GET["idseguidor"], $_SESSION["idusuarios"]);
}

// Redireciona de volta para a página de origem (ou home.php se não houver referer)
$referer = $_SERVER['HTTP_REFERER'] ?? '../pages/home.php';
header("Location: " . $referer);
exit;
