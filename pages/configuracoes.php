<?php
include("../login/incs/valida-sessao.php");
require_once "../login/src/ConexaoBD.php";
require_once "../login/src/UsuarioDAO.php";

$idusuario_logado = $_SESSION['idusuarios'];

$mensagem = $_GET['mensagem'] ?? '';
$erro = $_GET['erro'] ?? '';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações - SportForYou</title>
    <link rel="stylesheet" href="../assets/css/feed.css">
    <link rel="stylesheet" href="../assets/css/configuracoes.css">
    <link rel="stylesheet" href="../assets/css/tema-escuro.css">
    <link rel="stylesheet" href="../assets/css/responsivo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar esquerda -->
        <aside class="sidebar">
            <div class="logo">
                <img src="../assets/img/logo1.png" alt="Logo SportForYou">
            </div>
            <?php $paginaAtual = basename($_SERVER['PHP_SELF']); ?>
            <nav>
                <ul>
                    <li class="<?= $paginaAtual == 'home.php' ? 'ativo' : '' ?>"><a href="home.php"><i class="fa-solid fa-house"></i> Feed</a></li>
                    <li class="<?= $paginaAtual == 'mensagens.php' ? 'ativo' : '' ?>"><a href="mensagens.php"><i class="fa-solid fa-message"></i> Mensagens</a></li>
                    <li class="<?= $paginaAtual == 'eventos.php' ? 'ativo' : '' ?>"><a href="eventos.php"><i class="fa-solid fa-calendar-days"></i> Eventos</a></li>
                    <li class="<?= $paginaAtual == 'configuracoes.php' ? 'ativo' : '' ?>"><a href="configuracoes.php"><i class="fa-solid fa-gear"></i> Configurações</a></li>
                </ul>
            </nav>
            
            <div class="usuario">
                <div class="usuario-topo"></div>
                <div class="usuario-conteudo">
                    <a href="perfil.php?id=<?= $_SESSION['idusuarios'] ?>" class="perfil-link-usuario" style="display: flex; align-items: center; text-decoration: none; color: inherit; flex: 1;">
                        <img src="../login/uploads/<?= htmlspecialchars($_SESSION['foto_perfil']) ?>" alt="Foto de perfil">
                        <div class="user-info">
                            <span class="nome"><?= htmlspecialchars($_SESSION['nome']) ?></span>
                            <span class="nome_usuario">@<?= htmlspecialchars($_SESSION['nome_usuario']) ?></span>
                        </div>
                    </a>
                    <a href="../login/logout.php" class="logout" title="Sair" style="margin-left: auto;">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Conteúdo principal -->
        <main class="configuracoes-main">
            <?php if ($mensagem): ?>
                <div class="alert alert-success"><?= htmlspecialchars($mensagem) ?></div>
            <?php endif; ?>

            <?php if ($erro): ?>
                <div class="alert alert-error"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <!-- Card Header Configurações -->
            <div class="config-card header-card">
                <div class="card-header-content">
                    <i class="fa-solid fa-gear"></i>
                    <h1>Configurações</h1>
                </div>
            </div>

            <!-- Card Tema -->
            <div class="config-card">
                <h2 class="card-title">Tema</h2>
                <div class="theme-options">
                    <label class="radio-option">
                        <input type="radio" name="tema" value="claro" id="tema-claro" checked>
                        <span class="radio-custom"></span>
                        <span class="radio-label">Claro</span>
                    </label>
                    <label class="radio-option">
                        <input type="radio" name="tema" value="escuro" id="tema-escuro">
                        <span class="radio-custom"></span>
                        <span class="radio-label">Escuro</span>
                    </label>
                </div>
            </div>

            <!-- Card Alterar Senha -->
            <div class="config-card">
                <h2 class="card-title">Alterar senha</h2>
                <form method="POST" action="../actions/alterar_senha.php" class="senha-form" id="form-senha">
                    <div class="form-group">
                        <label for="nova_senha">Nova senha</label>
                        <input type="password" id="nova_senha" name="nova_senha" required minlength="6" placeholder="Digite sua nova senha">
                    </div>
                    <div class="form-group">
                        <label for="confirmar_senha">Confirmar senha</label>
                        <input type="password" id="confirmar_senha" name="confirmar_senha" required minlength="6" placeholder="Confirme sua nova senha">
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-salvar">
                            <i class="fa-solid fa-floppy-disk"></i> Salvar
                        </button>
                    </div>
                </form>
            </div>

            <!-- Card Sair -->
            <div class="config-card logout-card">
                <h2 class="card-title">Sair</h2>
                <p class="logout-description">Encerrar Sessão</p>
                <a href="../login/logout.php" class="btn-sair">
                    <span>Sair</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </main>
    </div>

    <script src="../assets/js/tema.js"></script>
    <script src="../assets/js/mobile-menu.js"></script>
    <script src="../assets/js/configuracoes.js"></script>
</body>
</html>
