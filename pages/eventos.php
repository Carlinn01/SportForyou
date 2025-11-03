<?php
include("../login/incs/valida-sessao.php");
require_once "../login/src/ConexaoBD.php";
require_once "../login/src/EventoDAO.php";
require_once "../login/src/UsuarioDAO.php";

$idusuario_logado = $_SESSION['idusuarios'];

// Busca todos os eventos
$eventos = EventoDAO::listarTodos();

// Busca interesses do usuário para cada evento
$interessesDoUsuario = [];
foreach ($eventos as $evento) {
    $interessesDoUsuario[$evento['idevento']] = EventoDAO::temInteresse($evento['idevento'], $idusuario_logado);
}

// Busca sugestões de usuários
$sugestoes = UsuarioDAO::listarSugestoes($idusuario_logado, 5);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos - SportForYou</title>
    <link rel="stylesheet" href="../assets/css/feed.css">
    <link rel="stylesheet" href="../assets/css/eventos.css">
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
        <main class="feed">
            <header class="eventos-header">
                <h1>EVENTOS NA SUA REGIÃO</h1>
                <div class="header-actions">
                    <a href="../actions/criar_evento.php" class="btn-cadastrar-eventos">
                        <i class="fa-solid fa-plus"></i> Cadastrar Eventos
                    </a>
                    <div class="icons">
                        <a href="mensagens.php"><i class="fa-solid fa-message"></i></a>
                        <a href="home.php"><i class="fa-regular fa-bell"></i></a>
                    </div>
                </div>
            </header>

            <?php 
            $mensagem = $_GET['mensagem'] ?? '';
            $erro = $_GET['erro'] ?? '';
            ?>
            
            <?php if ($mensagem): ?>
                <div class="alert alert-success"><?= htmlspecialchars($mensagem) ?></div>
            <?php endif; ?>

            <?php if ($erro): ?>
                <div class="alert alert-error"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <!-- Grid de Eventos -->
            <div class="eventos-grid">
                <?php if (empty($eventos)): ?>
                    <div class="sem-eventos">
                        <i class="fa-solid fa-calendar-xmark"></i>
                        <p>Nenhum evento cadastrado ainda.</p>
                        <a href="../actions/criar_evento.php" class="btn-cadastrar-eventos">Cadastrar Primeiro Evento</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($eventos as $evento): 
                        $temInteresse = isset($interessesDoUsuario[$evento['idevento']]) && $interessesDoUsuario[$evento['idevento']];
                        $dataFormatada = date('d/m/Y', strtotime($evento['data_evento']));
                    ?>
                        <div class="evento-card">
                            <?php if ($evento['foto']): ?>
                                <img src="../login/uploads/<?= htmlspecialchars($evento['foto']) ?>" alt="<?= htmlspecialchars($evento['titulo']) ?>" class="evento-imagem">
                            <?php else: ?>
                                <div class="evento-sem-imagem">
                                    <i class="fa-solid fa-calendar-check"></i>
                                </div>
                            <?php endif; ?>
                            <div class="evento-content">
                                <h3 class="evento-titulo"><?= htmlspecialchars($evento['titulo']) ?></h3>
                                <p class="evento-categoria"><?= htmlspecialchars($evento['tipo_esporte']) ?></p>
                                <div class="evento-info">
                                    <span class="evento-data">
                                        <i class="fa-regular fa-calendar"></i> <?= $dataFormatada ?>
                                    </span>
                                    <span class="evento-local">
                                        <i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($evento['local']) ?>
                                        <?php if ($evento['cidade']): ?>
                                            - <?= htmlspecialchars($evento['cidade']) ?>
                                            <?php if ($evento['estado']): ?>
                                                /<?= htmlspecialchars($evento['estado']) ?>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <?php if ($evento['organizador_id'] == $idusuario_logado): ?>
                                    <div class="evento-acoes">
                                        <a href="../actions/editar_evento.php?id=<?= $evento['idevento'] ?>" class="btn-editar-evento">
                                            <i class="fa-solid fa-pen"></i> Editar
                                        </a>
                                        <a href="../actions/deletar_evento.php?id=<?= $evento['idevento'] ?>" class="btn-deletar-evento" onclick="return confirm('Tem certeza que deseja deletar este evento?')">
                                            <i class="fa-solid fa-trash"></i> Deletar
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <button class="btn-interesse <?= $temInteresse ? 'ativo' : '' ?>" data-evento="<?= $evento['idevento'] ?>">
                                        <?= $temInteresse ? '<i class="fa-solid fa-check"></i> Tem interesse' : 'Tenho interesse' ?>
                                    </button>
                                <?php endif; ?>
                                <?php if ($evento['total_interessados'] > 0): ?>
                                    <span class="evento-interessados">
                                        <i class="fa-solid fa-users"></i> <?= $evento['total_interessados'] ?> interessado(s)
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>

        <!-- Lateral direita (Sugestões) -->
        <aside class="rightbar">
            <h3>Sugestões</h3>
            <ul class="sugestoes-list">
                <?php if (empty($sugestoes)): ?>
                    <li class="sem-sugestoes">Nenhuma sugestão disponível</li>
                <?php else: ?>
                    <?php foreach ($sugestoes as $user): 
                        $primeiroNome = explode(' ', $user['nome'])[0];
                        $estaSeguindo = false;
                        try {
                            require_once "../login/src/SeguidoDAO.php";
                            $estaSeguindo = SeguidoDAO::estaSeguindo($user['idusuarios'], $idusuario_logado);
                        } catch (Exception $e) {
                            // Ignora erro se SeguidoDAO não existir
                        }
                    ?>
                        <li class="sugestao-item">
                            <a href="perfil.php?id=<?= $user['idusuarios'] ?>" class="perfil-link">
                                <img src="../login/uploads/<?= htmlspecialchars($user['foto_perfil']) ?>" alt="<?= htmlspecialchars($user['nome_usuario']) ?>" width="40" height="40">
                                <div class="user-info">
                                    <span class="nome"><?= htmlspecialchars($primeiroNome) ?></span>
                                    <span class="nome_usuario">@<?= htmlspecialchars($user['nome_usuario']) ?></span>
                                </div>
                            </a>
                            <?php if ($estaSeguindo): ?>
                                <a href="../actions/deixar_seguir.php?idseguidor=<?= $user['idusuarios'] ?>" class="seguir-btn">Deixar de seguir</a>
                            <?php else: ?>
                                <a href="../actions/seguir.php?idseguidor=<?= $user['idusuarios'] ?>" class="seguir-btn">Seguir</a>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </aside>
    </div>

    <script src="../assets/js/tema.js"></script>
    <script src="../assets/js/mobile-menu.js"></script>
    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/eventos.js"></script>
</body>
</html>
