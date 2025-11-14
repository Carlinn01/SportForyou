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
    
    // Normaliza o email (remove espaços e converte para minúsculas)
    $emailOriginal = $email;
    $email = trim(strtolower($email));
    
    // Log para debug
    error_log("Tentativa de recuperação de senha para email: $email (original: $emailOriginal)");
    
    // Verifica se o e-mail existe (comparação case-insensitive)
    // Busca todos os campos necessários (idusuarios, email, nome)
    $sql = $pdo->prepare("SELECT idusuarios, email, nome FROM usuarios WHERE LOWER(TRIM(email)) = ?");
    $sql->execute([$email]);
    
    // Se não encontrar, tenta buscar todos os emails para debug
    if ($sql->rowCount() == 0) {
        // Tenta buscar sem normalização também (pode ter emails com maiúsculas no banco)
        $sql2 = $pdo->prepare("SELECT idusuarios, email, nome FROM usuarios WHERE email = ?");
        $sql2->execute([$emailOriginal]);
        
        if ($sql2->rowCount() == 0) {
            // Busca todos os emails para debug (apenas em desenvolvimento)
            error_log("Email não encontrado: $email. Buscando emails similares...");
            $sqlDebug = $pdo->query("SELECT email FROM usuarios LIMIT 10");
            $emailsDebug = $sqlDebug->fetchAll(PDO::FETCH_COLUMN);
            error_log("Emails no banco (primeiros 10): " . implode(", ", $emailsDebug));
            
            // Por segurança, não revela se o email existe ou não
            // Sempre mostra mensagem genérica
            header("Location: ../pages/solicitar-recuperacao.php?sucesso=" . urlencode("Se o e-mail estiver cadastrado, você receberá um link de recuperação."));
            exit;
        } else {
            // Encontrou com o email original, usa esse resultado
            $usuario = $sql2->fetch(PDO::FETCH_ASSOC);
            error_log("Email encontrado com formato original: " . $usuario['email']);
        }
    } else {
        $usuario = $sql->fetch(PDO::FETCH_ASSOC);
        error_log("Email encontrado: " . $usuario['email']);
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
                $pdo->exec("ALTER TABLE usuarios ADD COLUMN token_recuperacao VARCHAR(128) DEFAULT NULL");
            } catch (PDOException $e) {
                // Ignora se já existir (erro 1060)
                if ($e->getCode() != 1060) {
                    throw $e;
                }
            }
        } else {
            // Verifica se a coluna tem tamanho suficiente (token tem 64 caracteres)
            $colunaInfo = $pdo->query("SHOW COLUMNS FROM usuarios WHERE Field = 'token_recuperacao'")->fetch(PDO::FETCH_ASSOC);
            if ($colunaInfo && isset($colunaInfo['Type'])) {
                // Extrai o tamanho do VARCHAR
                if (preg_match('/varchar\((\d+)\)/i', $colunaInfo['Type'], $matches)) {
                    $tamanhoAtual = (int)$matches[1];
                    if ($tamanhoAtual < 128) {
                        // Aumenta o tamanho da coluna se necessário
                        try {
                            $pdo->exec("ALTER TABLE usuarios MODIFY COLUMN token_recuperacao VARCHAR(128) DEFAULT NULL");
                        } catch (PDOException $e) {
                            error_log("Aviso: Não foi possível aumentar o tamanho da coluna token_recuperacao: " . $e->getMessage());
                        }
                    }
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
            $pdo->exec("ALTER TABLE usuarios ADD COLUMN token_recuperacao VARCHAR(128) DEFAULT NULL");
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
    
    // Salva o token no banco (usa o ID do usuário encontrado)
    try {
        $idusuario = $usuario['idusuarios'];
        $emailBanco = $usuario['email']; // Usa o email exato do banco
        
        error_log("Salvando token para usuário ID: $idusuario, email: $emailBanco");
        
        $sqlUpdate = $pdo->prepare("UPDATE usuarios SET token_recuperacao = ?, token_expira = ? WHERE idusuarios = ?");
        $sqlUpdate->execute([$token, $expira, $idusuario]);
        
        // Verifica se o token foi salvo corretamente
        if ($sqlUpdate->rowCount() == 0) {
            error_log("ERRO: Nenhum registro foi atualizado ao salvar token para email: $emailBanco (ID: $idusuario)");
            header("Location: ../pages/solicitar-recuperacao.php?erro=" . urlencode("Erro ao salvar token de recuperação. Tente novamente."));
            exit;
        } else {
            error_log("Token salvo com sucesso para usuário ID: $idusuario");
        }
    } catch (PDOException $e) {
        // Se ainda der erro, as colunas não existem e precisam ser criadas manualmente
        error_log("Erro ao salvar token: " . $e->getMessage());
        header("Location: ../pages/solicitar-recuperacao.php?erro=" . urlencode("As colunas de recuperação de senha não foram criadas. Execute o SQL: ALTER TABLE usuarios ADD COLUMN token_recuperacao VARCHAR(128) DEFAULT NULL; ALTER TABLE usuarios ADD COLUMN token_expira DATETIME DEFAULT NULL;"));
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
    
    // Busca nome do usuário usando o ID (já temos o usuário da busca anterior)
    $nomeUsuario = $usuario['nome'] ?? 'Usuário';
    $emailDestino = $usuario['email']; // Usa o email exato do banco, não o normalizado
    
    error_log("Preparando envio de email para: $emailDestino (nome: $nomeUsuario)");
    
    // Tenta enviar e-mail
    $emailEnviado = false;
    $erroEmail = '';
    
    try {
        require_once "../login/src/EmailSender.php";
        $emailSender = new EmailSender();
        
        // Usa o email exato do banco, não o normalizado
        error_log("Tentando enviar email para: $emailDestino");
        $emailEnviado = $emailSender->enviarRecuperacaoSenha($emailDestino, $nomeUsuario, $link);
        
        if (!$emailEnviado) {
            $erroEmail = "Falha ao enviar e-mail. Verifique as configurações SMTP.";
            error_log("Email não foi enviado (retornou false) para: $emailDestino");
        } else {
            error_log("Email enviado com sucesso para: $emailDestino");
        }
    } catch (Exception $e) {
        $erroEmail = $e->getMessage();
        error_log("EXCEÇÃO ao enviar e-mail de recuperação para $emailDestino: " . $erroEmail);
        error_log("Stack trace: " . $e->getTraceAsString());
    } catch (Error $e) {
        // Captura erros fatais (ex: classe não encontrada)
        $erroEmail = "Erro fatal: " . $e->getMessage();
        error_log("ERRO FATAL ao enviar e-mail de recuperação para $emailDestino: " . $erroEmail);
        error_log("Stack trace: " . $e->getTraceAsString());
    }
    
    // SEMPRE salva o link na sessão (mesmo se o email foi enviado ou não)
    // Isso permite que o usuário use o link se o email não chegar
    $_SESSION['link_recuperacao'] = $link;
    $_SESSION['email_solicitado'] = $emailDestino;
    $_SESSION['token_recuperacao'] = $token;
    
    // Se e-mail foi enviado com sucesso
    if ($emailEnviado) {
        // Mostra mensagem de sucesso, mas também inclui o link na sessão para backup
        header("Location: ../pages/solicitar-recuperacao.php?sucesso=" . urlencode("Link de recuperação enviado para seu e-mail! Verifique sua caixa de entrada e a pasta de spam. Se não receber, use o link abaixo."));
    } else {
        // Se falhou, mostra erro detalhado (apenas em desenvolvimento)
        // Em produção, você pode querer apenas mostrar erro genérico
        $modoDesenvolvimento = true; // true = desenvolvimento (mostra link e erro na tela)
        
        // Log detalhado do erro para debug
        error_log("Falha ao enviar e-mail de recuperação para: $emailDestino");
        error_log("Erro detalhado: " . ($erroEmail ?: "Desconhecido"));
        error_log("Token gerado: $token");
        error_log("Link gerado: $link");
        
        // Verifica se o erro é de limite do Gmail
        $erroLimiteGmail = strpos($erroEmail, 'Daily user sending limit exceeded') !== false || 
                           strpos($erroEmail, 'sending limit exceeded') !== false;
        
        if ($modoDesenvolvimento) {
            // Modo desenvolvimento: mostra link e erro detalhado
            $mensagemErro = "E-mail não foi enviado. Erro: " . ($erroEmail ?: "Desconhecido");
            if ($erroLimiteGmail) {
                $mensagemErro = "⚠️ Limite diário de envio do Gmail excedido. Use o link abaixo para redefinir sua senha.";
            }
            header("Location: ../pages/solicitar-recuperacao.php?erro=" . urlencode($mensagemErro));
        } else {
            // Modo produção: mensagem genérica, mas o link será mostrado na página
            if ($erroLimiteGmail) {
                $mensagemErro = "⚠️ Limite diário de envio do Gmail excedido. Use o link abaixo para redefinir sua senha.";
            } else {
                $mensagemErro = "Erro ao enviar e-mail. Use o link abaixo para redefinir sua senha.";
            }
            header("Location: ../pages/solicitar-recuperacao.php?erro=" . urlencode($mensagemErro));
        }
    }
    exit;
    
} catch (PDOException $e) {
    error_log("Erro ao processar recuperação: " . $e->getMessage());
    header("Location: ../pages/solicitar-recuperacao.php?erro=" . urlencode("Erro ao processar solicitação. Tente novamente."));
    exit;
}

