<?php
include "../login/incs/valida-sessao.php";
require_once "../login/src/UsuarioDAO.php";
require_once "../login/src/SeguidoDAO.php";

$idusuario_logado = $_SESSION['idusuarios'];

if (!isset($_GET["nome"])) {
    $_GET["nome"] = "";
}

$usuarios = UsuarioDAO::buscarUsuarioNome($_GET["nome"]);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Usuários - SportForYou</title>
    <link rel="stylesheet" href="../assets/css/feed.css">
    <link rel="stylesheet" href="../assets/css/seguidores.css">
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
        <main class="seguidores-main">
            <div class="seguidores-container">
                <header class="seguidores-header">
                    <div class="header-content">
                        <h1><i class="fa-solid fa-users"></i> Buscar Usuários</h1>
                        <a href="home.php" class="btn-voltar">
                            <i class="fa-solid fa-arrow-left"></i> Voltar ao Feed
                        </a>
                    </div>
                </header>

                <div class="seguidores-content">
                    <!-- Formulário de Busca -->
                    <div class="busca-container">
                        <form action="seguidores.php" method="GET" class="busca-form">
                            <div class="search-input-wrapper">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input type="text" name="nome" placeholder="Buscar por nome ou usuário..." value="<?= htmlspecialchars($_GET['nome'] ?? '') ?>" class="search-input">
                                <button type="submit" class="search-btn">
                                    <i class="fa-solid fa-search"></i>
                                    Buscar
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Lista de Usuários -->
                    <div class="usuarios-section">
                        <h2 class="section-title">
                            <i class="fa-solid fa-user-group"></i> 
                            <?php if (!empty($_GET['nome'])): ?>
                                Resultados para "<?= htmlspecialchars($_GET['nome']) ?>"
                            <?php else: ?>
                                Todos os Usuários
                            <?php endif; ?>
                        </h2>

                        <?php if (empty($usuarios)): ?>
                            <div class="sem-resultados">
                                <i class="fa-solid fa-user-slash"></i>
                                <p>Nenhum usuário encontrado.</p>
                                <?php if (!empty($_GET['nome'])): ?>
                                    <p class="texto-secundario">Tente buscar com outro termo.</p>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="usuarios-list">
                                <?php foreach ($usuarios as $usuario): 
                                    // Não mostrar o próprio usuário na lista
                                    if ($usuario['idusuarios'] == $idusuario_logado) {
                                        continue;
                                    }
                                    $jaSeguindo = SeguidoDAO::estaSeguindo($usuario['idusuarios'], $idusuario_logado);
                                    $primeiroNome = explode(' ', $usuario['nome'])[0];
                                ?>
                                    <div class="usuario-card">
                                        <a href="perfil.php?id=<?= $usuario['idusuarios'] ?>" class="usuario-info">
                                            <img src="../login/uploads/<?= htmlspecialchars($usuario['foto_perfil']) ?>" alt="<?= htmlspecialchars($usuario['nome_usuario']) ?>" class="usuario-avatar">
                                            <div class="usuario-detalhes">
                                                <span class="usuario-nome"><?= htmlspecialchars($primeiroNome) ?></span>
                                                <span class="usuario-username">@<?= htmlspecialchars($usuario['nome_usuario']) ?></span>
                                            </div>
                                        </a>
                                        <div class="usuario-actions">
                                            <?php if ($jaSeguindo): ?>
                                                <a href="../actions/deixar_seguir.php?idseguidor=<?= $usuario['idusuarios'] ?>" class="btn-deixar-seguir">
                                                    <i class="fa-solid fa-user-check"></i>
                                                    Deixar de seguir
                                                </a>
                                            <?php else: ?>
                                                <a href="../actions/seguir.php?idseguidor=<?= $usuario['idusuarios'] ?>" class="btn-seguir">
                                                    <i class="fa-solid fa-user-plus"></i>
                                                    Seguir
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../assets/js/tema.js"></script>
    <script src="../assets/js/mobile-menu.js"></script>
</body>
</html>