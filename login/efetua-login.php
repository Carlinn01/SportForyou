<?php
    session_start();
    require "src/UsuarioDAO.php";

<<<<<<< HEAD
    if ($idusuarios = UsuarioDAO::validarUsuario($_POST)){    
        $_SESSION['usuario_email'] = $_POST['usuario_email'];  
        $_SESSION['idusuarios'] = $idusuarios ;     
=======
    if ($idusuarios=UsuarioDAO::validarUsuario($_POST)){  
        $_SESSION['usuario_email'] = $_POST['usuario_email'];
        $_SESSION['idusuarios'] = $idusuarios;
>>>>>>> a4206d787867b324e7efeddd06e438ec82c1ff17
        header("Location:../home.php");
    }else{
        $_SESSION['msg'] = "Usuário ou senha inválido.";
        
        header("Location:login.php");
    }
?>