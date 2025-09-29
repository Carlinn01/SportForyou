<?php
    session_start();
    
    if (!isset($_SESSION['email'])){
        $_SESSION['msg'] = "Para acessar essa página, é necessário fazer login.";
        header("Location:login.php");
    }    
?>