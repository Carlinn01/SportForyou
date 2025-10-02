<?php
    session_start();
    require "src/UsuarioDAO.php";
        
    UsuarioDAO::cadastrarUsuario($_POST);
    $_SESSION['msg'] = "Usuário cadastrado com sucesso!";
    header("Location:login.php");
?>