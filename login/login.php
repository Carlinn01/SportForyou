<?php
        session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - SportForYou</title>
  <link rel="stylesheet" href="../assets/css/login.css">
  <link rel="stylesheet" href="../assets/css/tema-escuro.css">
  <link rel="stylesheet" href="../assets/css/responsivo.css">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Staatliches&display=swap" rel="stylesheet">
  <script src="../assets/js/tema.js"></script>
</head>

<body>
  <main class="main-content">

    <form action="efetua-login.php" method="post" class="form-container">
        <div class="topo">
      <h2 class="main-title">SPORT <br>FOR <br>YOU</h2>
      <img src="../assets/img/Vector.png" alt="" class="vetor">
      </div>

      <?php

        if (isset($_SESSION['msg'])) {
            echo '<div class="alert">' . $_SESSION['msg'] . '</div>';
            unset($_SESSION['msg']);
        } else {
            echo '<div class="alert">Informe seu email e senha para entrar.</div>';
        }
      ?>

      <div class="form-group">
        <label for="usuario_email">Nome ou Email</label>
        <input type="text" id="usuario_email" name="usuario_email" placeholder="Digite seu nome ou email" required>
      </div>

      <div class="form-group">
        <label for="senha">Senha</label>
        <div class="password-wrapper">
          <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>
          <span class="toggle-password material-symbols-outlined">visibility_off</span>
        </div>
        <a href="../pages/solicitar-recuperacao.php" class="forgot">Esqueceu sua senha?</a>
      </div>

      <div class="botao">
        <button type="submit" class="submit-btn">Entrar</button>
      </div>

      <div class="login">
        <p>Ainda não tem conta? <span><a href="form-cadastra-usuario.php">Criar Conta</a></span></p>
      </div>
    </form>
  </main>

  <script>
    // Mostrar ou ocultar senha
    const togglePassword = document.querySelector('.toggle-password');
    const passwordInput = document.querySelector('#senha');

    togglePassword.addEventListener('click', () => {
      const isPassword = passwordInput.type === 'password';
      passwordInput.type = isPassword ? 'text' : 'password';
      togglePassword.textContent = isPassword ? 'visibility' : 'visibility_off';
    });
  </script>
</body>

</html>
