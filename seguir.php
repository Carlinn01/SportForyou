<?php
include("login/incs/valida-sessao.php");
require_once "login/src/SeguidoDAO.php";

if (isset($_GET["idseguidor"])) {
    SeguidoDAO::seguir($_SESSION["idusuario"], $_GET["idseguidor"]);
} 
    header("location:seguidores.php");
