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
        header("Location: ../pages/solicitar-recuperacao.php?erro=" . urlencode("E-mail não encontrado em nosso sistema."));
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
    
    // Em produção, aqui você enviaria o link por e-mail
    // Por enquanto, apenas redireciona com o link exibido
    $_SESSION['link_recuperacao'] = $link;
    $_SESSION['email_solicitado'] = $email;
    
    header("Location: ../pages/solicitar-recuperacao.php?sucesso=" . urlencode("Link de recuperação gerado! Em produção, este link seria enviado por e-mail. Link: $link"));
    exit;
    
} catch (PDOException $e) {
    error_log("Erro ao processar recuperação: " . $e->getMessage());
    header("Location: ../pages/solicitar-recuperacao.php?erro=" . urlencode("Erro ao processar solicitação. Tente novamente."));
    exit;
}

