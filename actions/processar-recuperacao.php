<?php
session_start();
require_once "../login/src/ConexaoBD.php";

$email = $_POST['email'] ?? '';

if (empty($email)) {
    header("Location: ../pages/solicitar-recuperacao.php?erro=" . urlencode("Por favor, informe seu e-mail."));
    exit;
}

try {
    $pdo = ConexaoBD::conectar();
    
    // Verifica se o e-mail existe
    $sql = $pdo->prepare("SELECT idusuarios FROM usuarios WHERE email = ?");
    $sql->execute([$email]);
    
    if ($sql->rowCount() == 0) {
        // Por segurança, não revela se o email existe ou não
        // Sempre mostra mensagem genérica
        header("Location: ../pages/solicitar-recuperacao.php?sucesso=" . urlencode("Se o e-mail estiver cadastrado, você receberá um link de recuperação."));
        exit;
    }
    
    // Gera token único
    $token = bin2hex(random_bytes(32));
    $expira = date("Y-m-d H:i:s", strtotime("+1 hour"));
    
    // Verifica se as colunas existem antes de atualizar
    try {
        $colunas = $pdo->query("SHOW COLUMNS FROM usuarios LIKE 'token_recuperacao'")->fetch();
        if (!$colunas) {
            // Adiciona as colunas se não existirem
            try {
                $pdo->exec("ALTER TABLE usuarios ADD COLUMN token_recuperacao VARCHAR(100) DEFAULT NULL");
            } catch (PDOException $e) {
                // Ignora se já existir (erro 1060)
                if ($e->getCode() != 1060) {
                    throw $e;
                }
            }
        }
        
        $colunas2 = $pdo->query("SHOW COLUMNS FROM usuarios LIKE 'token_expira'")->fetch();
        if (!$colunas2) {
            try {
                $pdo->exec("ALTER TABLE usuarios ADD COLUMN token_expira DATETIME DEFAULT NULL");
            } catch (PDOException $e) {
                // Ignora se já existir (erro 1060)
                if ($e->getCode() != 1060) {
                    throw $e;
                }
            }
        }
    } catch (PDOException $e) {
        // Se der erro, tenta continuar mesmo assim (pode ser que as colunas já existam)
        error_log("Aviso ao verificar colunas de recuperação: " . $e->getMessage());
    }
    
    // Verifica se as colunas existem antes de tentar atualizar
    $colunas_existem = false;
    try {
        $check = $pdo->query("SELECT token_recuperacao, token_expira FROM usuarios LIMIT 1");
        $colunas_existem = true;
    } catch (PDOException $e) {
        // Se as colunas não existirem, tenta criar novamente
        error_log("Colunas de recuperação não encontradas. Tentando criar...");
    }
    
    if (!$colunas_existem) {
        // Tenta criar as colunas uma última vez
        try {
            $pdo->exec("ALTER TABLE usuarios ADD COLUMN token_recuperacao VARCHAR(100) DEFAULT NULL");
        } catch (PDOException $e) {
            if ($e->getCode() != 1060) {
                error_log("Erro ao criar coluna token_recuperacao: " . $e->getMessage());
            }
        }
        
        try {
            $pdo->exec("ALTER TABLE usuarios ADD COLUMN token_expira DATETIME DEFAULT NULL");
        } catch (PDOException $e) {
            if ($e->getCode() != 1060) {
                error_log("Erro ao criar coluna token_expira: " . $e->getMessage());
            }
        }
    }
    
    // Salva o token no banco
    try {
        $sqlUpdate = $pdo->prepare("UPDATE usuarios SET token_recuperacao = ?, token_expira = ? WHERE email = ?");
        $sqlUpdate->execute([$token, $expira, $email]);
    } catch (PDOException $e) {
        // Se ainda der erro, as colunas não existem e precisam ser criadas manualmente
        error_log("Erro ao salvar token: " . $e->getMessage());
        header("Location: ../pages/solicitar-recuperacao.php?erro=" . urlencode("As colunas de recuperação de senha não foram criadas. Execute o SQL: ALTER TABLE usuarios ADD COLUMN token_recuperacao VARCHAR(100) DEFAULT NULL; ALTER TABLE usuarios ADD COLUMN token_expira DATETIME DEFAULT NULL;"));
        exit;
    }
    
    // Gera o link de redefinição
    $protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    
    // Usa SCRIPT_NAME para determinar o caminho base do projeto
    // $_SERVER['SCRIPT_NAME'] = /SportForyou-1/actions/processar-recuperacao.php
    $scriptPath = $_SERVER['SCRIPT_NAME'] ?? '';
    
    // Remove o nome do arquivo e o diretório 'actions'
    // Exemplo: /SportForyou-1/actions/processar-recuperacao.php -> /SportForyou-1
    $parts = explode('/', trim($scriptPath, '/'));
    
    // Remove o nome do arquivo (último elemento)
    if (count($parts) > 0) {
        array_pop($parts);
    }
    // Remove 'actions' (último elemento agora)
    if (count($parts) > 0 && end($parts) === 'actions') {
        array_pop($parts);
    }
    
    // Reconstrói o caminho base
    $baseDir = '/' . implode('/', $parts);
    
    // Normaliza o caminho (remove barras duplicadas)
    $baseDir = preg_replace('#/+#', '/', $baseDir);
    $baseDir = rtrim($baseDir, '/');
    
    // Garante que não há repetição de /pages/
    // Remove qualquer /pages/ que já exista no baseDir antes de adicionar
    $baseDir = preg_replace('#/pages/?$#', '', $baseDir); // Remove /pages do final se existir
    $baseDir = rtrim($baseDir, '/');
    
    // Constrói o caminho final: baseDir + /pages/redefinir-senha.php
    $caminhoFinal = $baseDir . '/pages/redefinir-senha.php';
    $caminhoFinal = preg_replace('#/+#', '/', $caminhoFinal); // Remove barras duplicadas
    
    // Constrói o link completo
    $link = "$protocolo://$host$caminhoFinal?token=$token";
    
    // Busca nome do usuário para personalizar o e-mail
    $sqlNome = $pdo->prepare("SELECT nome FROM usuarios WHERE email = ?");
    $sqlNome->execute([$email]);
    $usuario = $sqlNome->fetch(PDO::FETCH_ASSOC);
    $nomeUsuario = $usuario['nome'] ?? 'Usuário';
    
    // Tenta enviar e-mail
    $emailEnviado = false;
    $erroEmail = '';
    
    try {
        require_once "../login/src/EmailSender.php";
        $emailSender = new EmailSender();
        $emailEnviado = $emailSender->enviarRecuperacaoSenha($email, $nomeUsuario, $link);
        
        if (!$emailEnviado) {
            $erroEmail = "Falha ao enviar e-mail. Verifique as configurações SMTP.";
        }
    } catch (Exception $e) {
        $erroEmail = $e->getMessage();
        error_log("Erro ao enviar e-mail de recuperação para $email: " . $erroEmail);
    } catch (Error $e) {
        // Captura erros fatais (ex: classe não encontrada)
        $erroEmail = "Erro fatal: " . $e->getMessage();
        error_log("Erro fatal ao enviar e-mail de recuperação para $email: " . $erroEmail);
    }
    
    // Se e-mail foi enviado com sucesso
    if ($emailEnviado) {
        header("Location: ../pages/solicitar-recuperacao.php?sucesso=" . urlencode("Link de recuperação enviado para seu e-mail! Verifique sua caixa de entrada e a pasta de spam."));
    } else {
        // Se falhou, mostra erro detalhado (apenas em desenvolvimento)
        // Em produção, você pode querer apenas mostrar erro genérico
        $modoDesenvolvimento = true; // Mude para false em produção
        
        if ($modoDesenvolvimento) {
            // Modo desenvolvimento: mostra link e erro
            $_SESSION['link_recuperacao'] = $link;
            $_SESSION['email_solicitado'] = $email;
            $mensagemErro = "E-mail não foi enviado. Erro: " . ($erroEmail ?: "Desconhecido") . " | Link: $link";
            header("Location: ../pages/solicitar-recuperacao.php?erro=" . urlencode($mensagemErro));
        } else {
            // Modo produção: apenas mensagem genérica
            error_log("Falha ao enviar e-mail de recuperação para: $email - Erro: $erroEmail");
            header("Location: ../pages/solicitar-recuperacao.php?erro=" . urlencode("Erro ao enviar e-mail. Tente novamente mais tarde ou entre em contato com o suporte."));
        }
    }
    exit;
    
} catch (PDOException $e) {
    error_log("Erro ao processar recuperação: " . $e->getMessage());
    header("Location: ../pages/solicitar-recuperacao.php?erro=" . urlencode("Erro ao processar solicitação. Tente novamente."));
    exit;
}

