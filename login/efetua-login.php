<?php
    session_start();
    require "src/UsuarioDAO.php";
    require "src/RateLimiter.php";

    // Rate limiting - máximo 5 tentativas por minuto por IP
    $ip = RateLimiter::obterIP();
    if (!RateLimiter::verificar($ip . '_login', 5, 60)) {
        $_SESSION['msg'] = "Muitas tentativas de login. Aguarde 1 minuto e tente novamente.";
        header("Location:login.php");
        exit;
    }

    if ($usuarios=UsuarioDAO::validarUsuario($_POST)){  
        // Regenera ID de sessão após login bem-sucedido
        session_regenerate_id(true);
        
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