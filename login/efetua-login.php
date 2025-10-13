<?php
    session_start();
    require "src/UsuarioDAO.php";

    if ($usuarios=UsuarioDAO::validarUsuario($_POST)){  
        $_SESSION['usuario_email'] = $_POST['usuario_email'];
        $_SESSION['idusuarios'] = $usuarios['idusuarios'];
        $_SESSION['nome'] = $usuarios['nome'];
        $_SESSION['foto_perfil'] = $usuarios['foto_perfil'];
        $_SESSION['nome_usuario'] = $usuarios['nome_usuario'];
        $_SESSION['email'] = $usuarios['email'];
        $_SESSION['nascimento'] = $usuarios['nascimento'];
        header("Location:../home.php");
    }else{
        $_SESSION['msg'] = "Usuário ou senha inválido.";
        
        header("Location:login.php");
    }
?>