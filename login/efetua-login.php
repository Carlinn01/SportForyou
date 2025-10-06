<?php
    session_start();
    require "src/UsuarioDAO.php";

    if ($idusuarios = UsuarioDAO::validarUsuario($_POST)){    
        $_SESSION['usuario_email'] = $_POST['usuario_email'];  
        $_SESSION['idusuarios'] = $idusuarios ;     
        header("Location:../home.php");
    }else{
        $_SESSION['msg'] = "Usuário ou senha inválido.";
        
        header("Location:login.php");
    }
?>