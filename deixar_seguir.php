<?php
include("login/incs/valida-sessao.php");
require_once "login/src/SeguidoDAO.php";

if (isset($_GET["idseguidor"])) {
    SeguidoDAO::deixarDeSeguir($_SESSION["idusuarios"], $_GET["idseguidor"]);
}

header("location:seguidores.php");
exit;
