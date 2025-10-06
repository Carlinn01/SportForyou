<?php
    session_start();
    require "src/UsuarioDAO.php";

    if (UsuarioDAO::validarUsuario($_POST)){    
        $_SESSION['usuario_email'] = $_POST['usuario_email'];        
        header("Location:../home.php");
    }else{
        $_SESSION['msg'] = "Usuário ou senha inválido.";
        
        header("Location:login.php");
    }
?>