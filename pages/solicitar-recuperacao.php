<!-- novo "método" de recuperação de senha mas estava com limite de email então tirei -->
<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha - SportForYou</title>
    <link rel="stylesheet" href="../assets/css/login.css">
    <link rel="stylesheet" href="../assets/css/responsivo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        function copiarLink(link) {
            navigator.clipboard.writeText(link).then(function() {
                alert('Link copiado para a área de transferência!');
            }).catch(function() {
                // Fallback para navegadores mais antigos
                const textarea = document.createElement('textarea');
                textarea.value = link;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                alert('Link copiado para a área de transferência!');
            });
        }
    </script>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <img src="../assets/img/logo1.png" alt="Logo SportForYou" class="logo-img">
                <h2>Recuperar Senha</h2>
                <p>Digite seu e-mail para receber o link de recuperação</p>
            </div>

            <?php if (isset($_GET['erro'])): 
                $mensagemErro = $_GET['erro'];
                $link = '';
                // Tenta extrair o link da mensagem de erro também
                if (preg_match('/Link:\s*(https?:\/\/[^\s]+)/', $mensagemErro, $matches)) {
                    $link = $matches[1];
                    $mensagemErro = str_replace('Link: ' . $link, '', $mensagemErro);
                }
                // Verifica se há link na sessão
                if (empty($link) && isset($_SESSION['link_recuperacao'])) {
                    $link = $_SESSION['link_recuperacao'];
                    unset($_SESSION['link_recuperacao']);
                }
            ?>
                <div class="alert alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <div style="flex: 1;">
                        <p style="margin: 0 0 10px 0;"><?= htmlspecialchars($mensagemErro) ?></p>
                        <?php if ($link): ?>
                            <div style="background: rgba(255, 255, 255, 0.2); padding: 12px; border-radius: 8px; margin-top: 10px;">
                                <strong style="display: block; margin-bottom: 8px; font-size: 13px; color: white;">⚠️ O e-mail não foi enviado, mas você pode usar o link abaixo para redefinir sua senha:</strong>
                                <a href="<?= htmlspecialchars($link) ?>" 
                                   style="color: white; text-decoration: underline; word-break: break-all; font-size: 13px; display: block; margin: 10px 0;">
                                    <?= htmlspecialchars($link) ?>
                                </a>
                                <button onclick="copiarLink('<?= htmlspecialchars($link) ?>')" 
                                        style="margin-top: 10px; padding: 8px 15px; background: rgba(255, 255, 255, 0.3); border: 1px solid white; border-radius: 6px; color: white; cursor: pointer; font-size: 12px; font-weight: 600;">
                                    <i class="fa-solid fa-copy"></i> Copiar Link
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['sucesso'])): 
                // Extrai o link da mensagem se existir
                $mensagem = $_GET['sucesso'];
                $link = '';
                // Tenta extrair o link da mensagem
                if (preg_match('/Link:\s*(https?:\/\/[^\s]+)/', $mensagem, $matches)) {
                    $link = $matches[1];
                    $mensagem = str_replace('Link: ' . $link, '', $mensagem);
                }
                // Verifica se há link na sessão (sempre disponível como backup)
                if (empty($link) && isset($_SESSION['link_recuperacao'])) {
                    $link = $_SESSION['link_recuperacao'];
                }
                // Remove qualquer "pages" repetido do link e normaliza
                if ($link) {
                    // Remove múltiplas ocorrências de /pages/
                    $link = preg_replace('#(/pages/)+#', '/pages/', $link);
                    // Remove barras duplicadas
                    $link = preg_replace('#([^:])/+#', '$1/', $link);
                    // Garante que não há /pages/ no meio do caminho repetido
                    $link = preg_replace('#/pages/pages/#', '/pages/', $link);
                }
            ?>
                <div class="alert alert-success">
                    <i class="fa-solid fa-circle-check"></i>
                    <div style="flex: 1;">
                        <p style="margin: 0 0 10px 0;"><?= htmlspecialchars($mensagem) ?></p>
                        <?php if ($link): ?>
                            <div style="background: rgba(255, 255, 255, 0.2); padding: 12px; border-radius: 8px; margin-top: 10px;">
                                <strong style="display: block; margin-bottom: 8px; font-size: 13px;">🔗 Link de recuperação (use se não receber o e-mail):</strong>
                                <a href="<?= htmlspecialchars($link) ?>" 
                                   style="color: white; text-decoration: underline; word-break: break-all; font-size: 13px; display: block; margin: 10px 0;">
                                    <?= htmlspecialchars($link) ?>
                                </a>
                                <button onclick="copiarLink('<?= htmlspecialchars($link) ?>')" 
                                        style="margin-top: 10px; padding: 8px 15px; background: rgba(255, 255, 255, 0.3); border: 1px solid white; border-radius: 6px; color: white; cursor: pointer; font-size: 12px; font-weight: 600;">
                                    <i class="fa-solid fa-copy"></i> Copiar Link
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <form action="../actions/processar-recuperacao.php" method="post" class="login-form">
                <div class="form-group">
                    <label for="email">
                        <i class="fa-solid fa-envelope"></i> E-mail
                    </label>
                    <input type="email" id="email" name="email" required placeholder="seu@email.com">
                </div>

                <button type="submit" class="btn-login">
                    <i class="fa-solid fa-paper-plane"></i> Enviar Link de Recuperação
                </button>
            </form>

            <div class="login-footer">
                <a href="../login/login.php">
                    <i class="fa-solid fa-arrow-left"></i> Voltar para Login
                </a>
            </div>
        </div>
    </div>
</body>
</html>

