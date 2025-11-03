<?php
require_once "../login/src/ConexaoBD.php";

$token = $_GET['token'] ?? '';

if (empty($token)) {
    header("Location: ../login/login.php?erro=" . urlencode("Token não fornecido!"));
    exit;
}

try {
    $pdo = ConexaoBD::conectar();
    
    // Verifica se o token é válido e não expirou
    $sql = $pdo->prepare("SELECT idusuarios FROM usuarios WHERE token_recuperacao = ? AND token_expira > NOW()");
    $sql->execute([$token]);
    
    if ($sql->rowCount() == 0) {
        header("Location: ../login/login.php?erro=" . urlencode("Token inválido ou expirado!"));
        exit;
    }
    
    $usuario = $sql->fetch(PDO::FETCH_ASSOC);
    $token_valido = true;
} catch (PDOException $e) {
    error_log("Erro ao verificar token: " . $e->getMessage());
    header("Location: ../login/login.php?erro=" . urlencode("Erro ao verificar token. Tente novamente."));
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha - SportForYou</title>
    <link rel="stylesheet" href="../assets/css/login.css">
    <link rel="stylesheet" href="../assets/css/tema-escuro.css">
    <link rel="stylesheet" href="../assets/css/responsivo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="../assets/js/tema.js"></script>
</head>
<body>
    <script>
        function togglePassword(inputId, iconElement) {
            const input = document.getElementById(inputId);
            const icon = iconElement.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <img src="../assets/img/logo1.png" alt="Logo SportForYou" class="logo-img">
                <h2>Redefinir Senha</h2>
                <p>Digite sua nova senha</p>
            </div>

            <?php if (isset($_GET['erro'])): ?>
                <div class="alert alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?= htmlspecialchars($_GET['erro']) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['sucesso'])): ?>
                <div class="alert alert-success">
                    <i class="fa-solid fa-circle-check"></i>
                    <?= htmlspecialchars($_GET['sucesso']) ?>
                </div>
            <?php endif; ?>

            <form action="../actions/atualizar-senha.php" method="post" class="login-form">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                
                <div class="form-group">
                    <label for="nova_senha">
                        <i class="fa-solid fa-lock"></i> Nova Senha
                    </label>
                    <div class="password-wrapper">
                        <input type="password" id="nova_senha" name="nova_senha" required placeholder="Mínimo 6 caracteres" minlength="6">
                        <span class="toggle-password" onclick="togglePassword('nova_senha', this)">
                            <i class="fa-solid fa-eye"></i>
                        </span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirmar_senha">
                        <i class="fa-solid fa-lock"></i> Confirmar Senha
                    </label>
                    <div class="password-wrapper">
                        <input type="password" id="confirmar_senha" name="confirmar_senha" required placeholder="Digite a senha novamente" minlength="6">
                        <span class="toggle-password" onclick="togglePassword('confirmar_senha', this)">
                            <i class="fa-solid fa-eye"></i>
                        </span>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fa-solid fa-check"></i> Atualizar Senha
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

