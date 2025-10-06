<?php
    session_start();
    
    if (!isset($_SESSION['usuario_email'])){
        $_SESSION['msg'] = "Para acessar essa página, é necessário fazer login.";
        header("Location:/login/login.php");
    }    
?>