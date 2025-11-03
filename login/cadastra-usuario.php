<?php
    session_start();
    require "src/UsuarioDAO.php";
        
    try {
        // Validação básica dos dados
        if (empty($_POST['email']) || empty($_POST['senha']) || empty($_POST['nome']) || empty($_POST['nome_usuario'])) {
            $_SESSION['msg'] = "Por favor, preencha todos os campos obrigatórios.";
            $_SESSION['msg_tipo'] = 'erro';
            header("Location:form-cadastra-usuario.php");
            exit;
        }

        // Sanitiza o nome de usuário (remove espaços e caracteres especiais)
        $nome_usuario = trim($_POST['nome_usuario']);
        $nome_usuario = preg_replace('/[^a-zA-Z0-9_]/', '', $nome_usuario);
        
        if (empty($nome_usuario)) {
            $_SESSION['msg'] = "Nome de usuário inválido. Use apenas letras, números e underscore.";
            $_SESSION['msg_tipo'] = 'erro';
            header("Location:form-cadastra-usuario.php");
            exit;
        }

        $_POST['nome_usuario'] = $nome_usuario;
        
        UsuarioDAO::cadastrarUsuario($_POST);
        $_SESSION['msg'] = "Usuário cadastrado com sucesso!";
        $_SESSION['msg_tipo'] = 'sucesso';
        header("Location:login.php");
    } catch (Exception $e) {
        $_SESSION['msg'] = $e->getMessage();
        $_SESSION['msg_tipo'] = 'erro';
        header("Location:form-cadastra-usuario.php");
    }
?>