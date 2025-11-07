<?php
/**
 * Classe para envio de e-mails usando PHPMailer
 * 
 * Requer: composer require phpmailer/phpmailer
 * Ou baixar manualmente: https://github.com/PHPMailer/PHPMailer
 */

// Tenta carregar via Composer primeiro
if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/../../PHPMailer-7.0.0/src/Exception.php')) {
    // Se PHPMailer estiver na raiz do projeto (PHPMailer-7.0.0/)
    require_once __DIR__ . '/../../PHPMailer-7.0.0/src/Exception.php';
    require_once __DIR__ . '/../../PHPMailer-7.0.0/src/PHPMailer.php';
    require_once __DIR__ . '/../../PHPMailer-7.0.0/src/SMTP.php';
} elseif (file_exists(__DIR__ . '/PHPMailer/src/Exception.php')) {
    // Se PHPMailer estiver em login/src/PHPMailer/
    require_once __DIR__ . '/PHPMailer/src/Exception.php';
    require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
    require_once __DIR__ . '/PHPMailer/src/SMTP.php';
} else {
    // Se PHPMailer não estiver instalado, lança erro
    throw new Exception("PHPMailer não encontrado! 
    Opções:
    1. Instale via Composer: composer require phpmailer/phpmailer
    2. Baixe e coloque em: login/src/PHPMailer/ ou na raiz como PHPMailer-7.0.0/");
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailSender {
    
    private $mailer;
    private $config;
    
    public function __construct() {
        $this->mailer = new PHPMailer(true);
        $this->loadConfig();
        $this->configureMailer();
    }
    
    /**
     * Carrega configurações do arquivo de config
     */
    private function loadConfig() {
        $configFile = __DIR__ . '/email_config.php';
        
        if (file_exists($configFile)) {
            $this->config = require $configFile;
        } else {
            // Configurações padrão (ajuste conforme necessário)
            $this->config = [
                'smtp_host' => 'smtp.gmail.com',
                'smtp_port' => 587,
                'smtp_secure' => 'tls', // 'tls' ou 'ssl'
                'smtp_username' => '',
                'smtp_password' => '',
                'from_email' => 'noreply@sportforyou.com',
                'from_name' => 'SportForYou',
                'reply_to' => 'suporte@sportforyou.com'
            ];
        }
    }
    
    /**
     * Configura o PHPMailer com as credenciais SMTP
     */
    private function configureMailer() {
        try {
            // Verifica se OpenSSL está disponível
            if (!extension_loaded('openssl')) {
                throw new Exception("A extensão OpenSSL do PHP não está habilitada. Habilite no php.ini: extension=openssl");
            }
            
            // Configurações do servidor
            $this->mailer->isSMTP();
            $this->mailer->Host = $this->config['smtp_host'];
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $this->config['smtp_username'];
            $this->mailer->Password = $this->config['smtp_password'];
            
            // Se OpenSSL não estiver disponível, tenta sem segurança (não recomendado, mas funciona)
            if (extension_loaded('openssl')) {
                $this->mailer->SMTPSecure = $this->config['smtp_secure'];
            } else {
                // Sem segurança (apenas para desenvolvimento local)
                $this->mailer->SMTPSecure = false;
                $this->mailer->SMTPAutoTLS = false;
            }
            
            $this->mailer->Port = $this->config['smtp_port'];
            $this->mailer->CharSet = 'UTF-8';
            
            // Desabilita verificação SSL (apenas para desenvolvimento - NÃO use em produção!)
            $this->mailer->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            
            // Remetente
            $this->mailer->setFrom($this->config['from_email'], $this->config['from_name']);
            $this->mailer->addReplyTo($this->config['reply_to'], $this->config['from_name']);
            
            // Configurações adicionais
            $this->mailer->isHTML(true);
            
        } catch (Exception $e) {
            error_log("Erro ao configurar EmailSender: " . $e->getMessage());
            throw $e; // Re-lança a exceção para ser tratada
        }
    }
    
    /**
     * Envia e-mail de recuperação de senha
     * 
     * @param string $emailDestino E-mail do destinatário
     * @param string $nome Nome do usuário
     * @param string $link Link de recuperação
     * @return bool True se enviado com sucesso
     */
    public function enviarRecuperacaoSenha($emailDestino, $nome, $link) {
        try {
            // Limpa destinatários anteriores
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();
            
            // Adiciona destinatário
            $this->mailer->addAddress($emailDestino, $nome);
            
            // Assunto
            $this->mailer->Subject = 'Recuperação de Senha - SportForYou';
            
            // Corpo do e-mail (HTML)
            $this->mailer->Body = $this->getTemplateRecuperacao($nome, $link);
            
            // Versão texto simples (para clientes que não suportam HTML)
            $this->mailer->AltBody = $this->getTemplateRecuperacaoTexto($nome, $link);
            
            // Envia o e-mail
            $this->mailer->send();
            
            error_log("E-mail de recuperação enviado com sucesso para: $emailDestino");
            return true;
            
        } catch (Exception $e) {
            $erro = "Erro PHPMailer: " . $this->mailer->ErrorInfo . " | Exception: " . $e->getMessage();
            error_log("Erro ao enviar e-mail de recuperação para $emailDestino: " . $erro);
            throw new Exception($erro);
        }
    }
    
    /**
     * Template HTML do e-mail de recuperação
     */
    private function getTemplateRecuperacao($nome, $link) {
        $primeiroNome = explode(' ', $nome)[0];
        
        return "
        <!DOCTYPE html>
        <html lang='pt-BR'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                }
                .container {
                    background: #f9f9f9;
                    border-radius: 10px;
                    padding: 30px;
                    border: 1px solid #ddd;
                }
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                }
                .logo {
                    font-size: 28px;
                    font-weight: bold;
                    color: #008EE0;
                    margin-bottom: 10px;
                }
                .content {
                    background: white;
                    padding: 25px;
                    border-radius: 8px;
                    margin-bottom: 20px;
                }
                .button {
                    display: inline-block;
                    background: linear-gradient(135deg, #008EE0, #00d4ff);
                    color: white !important;
                    padding: 15px 30px;
                    text-decoration: none;
                    border-radius: 8px;
                    font-weight: bold;
                    margin: 20px 0;
                    text-align: center;
                }
                .warning {
                    background: #fff3cd;
                    border-left: 4px solid #ffc107;
                    padding: 15px;
                    margin: 20px 0;
                    border-radius: 4px;
                }
                .footer {
                    text-align: center;
                    color: #666;
                    font-size: 12px;
                    margin-top: 30px;
                    padding-top: 20px;
                    border-top: 1px solid #ddd;
                }
                .link-fallback {
                    word-break: break-all;
                    color: #008EE0;
                    font-size: 12px;
                    margin-top: 15px;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <div class='logo'>SPORT FOR YOU</div>
                </div>
                
                <div class='content'>
                    <h2>Olá, {$primeiroNome}!</h2>
                    
                    <p>Você solicitou a recuperação de senha da sua conta no SportForYou.</p>
                    
                    <p>Clique no botão abaixo para redefinir sua senha:</p>
                    
                    <div style='text-align: center;'>
                        <a href='{$link}' class='button'>Redefinir Senha</a>
                    </div>
                    
                    <div class='warning'>
                        <strong>⚠️ Importante:</strong>
                        <ul style='margin: 10px 0; padding-left: 20px;'>
                            <li>Este link expira em <strong>1 hora</strong></li>
                            <li>O link só pode ser usado <strong>uma vez</strong></li>
                            <li>Se você não solicitou esta recuperação, ignore este e-mail</li>
                        </ul>
                    </div>
                    
                    <p style='margin-top: 20px;'>Se o botão não funcionar, copie e cole o link abaixo no seu navegador:</p>
                    <p class='link-fallback'>{$link}</p>
                </div>
                
                <div class='footer'>
                    <p>Este é um e-mail automático, por favor não responda.</p>
                    <p>© " . date('Y') . " SportForYou - Todos os direitos reservados</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
    
    /**
     * Template texto simples do e-mail de recuperação
     */
    private function getTemplateRecuperacaoTexto($nome, $link) {
        $primeiroNome = explode(' ', $nome)[0];
        
        return "
Olá, {$primeiroNome}!

Você solicitou a recuperação de senha da sua conta no SportForYou.

Clique no link abaixo para redefinir sua senha:
{$link}

IMPORTANTE:
- Este link expira em 1 hora
- O link só pode ser usado uma vez
- Se você não solicitou esta recuperação, ignore este e-mail

Este é um e-mail automático, por favor não responda.

© " . date('Y') . " SportForYou
        ";
    }
}

