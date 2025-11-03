<?php
include "login/incs/valida-sessao.php";
require_once "login/src/StoryDAO.php";
require_once "login/src/PostagemDAO.php";
require_once "login/src/UsuarioDAO.php";
require_once "login/src/CurtiuDAO.php";
require_once "login/src/ComentarioDAO.php";
require_once "login/src/ConexaoBD.php";

$idusuario_logado = $_SESSION['idusuarios'];

if (isset($_GET['id'])) {
    UsuarioDAO::marcarComoLida($_GET['id']);
}

$notificacoes = UsuarioDAO::listarNotificacoes($idusuario_logado);

$stories = StoryDAO::listarRecentes();
$sugestoes = UsuarioDAO::listarSugestoes($idusuario_logado);

$feed = $_GET['feed'] ?? 'seguindo'; // padrão: seguindo

if ($feed === 'seguindo') {
    $postagens = PostagemDAO::listarDeSeguidos($idusuario_logado);
} else {
    $postagens = PostagemDAO::listarTodas(); // Para Você
}

// Carrega todos os comentários de uma vez para evitar N+1 queries
$idsPostagens = array_column($postagens, 'idpostagem');
$comentariosPorPostagem = ComentarioDAO::listarComentariosPorPostagens($idsPostagens);

// Verifica quais postagens o usuário já curtiu
$curtidasDoUsuario = [];
if (!empty($idsPostagens)) {
    $pdo = ConexaoBD::conectar();
    $placeholders = implode(',', array_fill(0, count($idsPostagens), '?'));
    $sql = "SELECT idpostagem FROM curtidas WHERE idusuario = ? AND idpostagem IN ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $params = array_merge([$idusuario_logado], $idsPostagens);
    $stmt->execute($params);
    $curtidasDoUsuario = array_flip($stmt->fetchAll(PDO::FETCH_COLUMN));
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>SportForYou</title>
    <link rel="stylesheet" href="/css/feed.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>
<body>
  
    <div class="container">
        <!-- Sidebar esquerda -->
        <aside class="sidebar">
            <div class="logo">
                <img src="/img/logo1.png" alt="Logo SportForYou">
            </div>
            <?php $paginaAtual = basename($_SERVER['PHP_SELF']); ?>
<nav>
    <ul>
        <li class="<?= $paginaAtual == 'home.php' ? 'ativo' : '' ?>"><a href="home.php"><i class="fa-solid fa-house"></i> Feed</a></li>
        <li class="<?= $paginaAtual == 'esportes.php' ? 'ativo' : '' ?>"><a href="esportes.php"><i class="fa-solid fa-gamepad"></i> Esportes</a></li>
        <li class="<?= $paginaAtual == 'eventos.php' ? 'ativo' : '' ?>"><a href="eventos.php"><i class="fa-solid fa-calendar-days"></i> Eventos</a></li>
        <!-- <li class="<?= $paginaAtual == 'salvos.php' ? 'ativo' : '' ?>"><a href="salvos.php"><i class="fa-solid fa-star"></i> Salvos</a></li> -->
        <li class="<?= $paginaAtual == 'configuracoes.php' ? 'ativo' : '' ?>"><a href="configuracoes.php"><i class="fa-solid fa-gear"></i> Configurações</a></li>
    </ul>
</nav>

            
       <div class="usuario">
    <div class="usuario-topo"></div> <!-- linha cinza -->
    <div class="usuario-conteudo">
        <img src="/login/uploads/<?= htmlspecialchars($_SESSION['foto_perfil']) ?>" alt="Foto de perfil">
        <div class="user-info">
            <span class="nome"><?= htmlspecialchars($_SESSION['nome']) ?></span>
            <span class="nome_usuario">@<?= htmlspecialchars($_SESSION['nome_usuario']) ?></span>
        </div>
        <a href="/login/logout.php" class="logout" title="Sair">
            <i class="fa-solid fa-right-from-bracket"></i>
        </a>
    </div>
</div>
        </aside>

        <!-- Conteúdo principal -->
        <main class="feed">
            <header class="topbar">
<div class="search-container">
    <input type="text" id="search-input" placeholder="Pesquisar pessoas ">
    <button type="submit" class="botao"><i class="fa-solid fa-magnifying-glass"></i></button>
</div>
<div id="search-results" class="search-results"></div>


                <div class="icons">
    <i class="fa-solid fa-message"></i>
    <i class="fa-regular fa-bell" id="bell-icon"></i>
    <div id="notifications" class="notifications-dropdown">
        <?php if (!empty($notificacoes)): ?>
            <ul>
                <?php foreach ($notificacoes as $notificacao): ?>
                    <li>
                        <a href="ver-notificacao.php?id=<?= $notificacao['id'] ?>">
                            <p><?= htmlspecialchars($notificacao['mensagem']) ?></p>
                            <small><?= $notificacao['data'] ?></small>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Sem notificações novas.</p>
        <?php endif; ?>
    </div>
</div>

            </header>

            

           <div class="stories-container">
    <form class="add-story" action="enviar_story.php" method="POST" enctype="multipart/form-data">
        <label for="story-file">+</label>
        <input type="file" id="story-file" name="story" accept="image/*,video/*" onchange="this.form.submit()">
    </form>

    <div class="stories">
        <?php foreach ($stories as $story): ?>
            <div class="story" data-media="<?php echo $story['midia']; ?>" data-type="<?php echo $story['tipo']; ?>">
               <img src="/login/uploads/<?= htmlspecialchars($story['foto_perfil']) ?>" alt="<?= htmlspecialchars($story['nome']) ?>">

                <p><?php echo strtok($story['nome'], ' '); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="story-viewer hidden">
    <div class="progress-bar"></div>
    <div class="story-content"></div>
    <button class="story-close">✖</button>
    <div class="nav-left">&lt;</div>
    <div class="nav-right">&gt;</div>
</div>





            <div class="new-post">
    <form action="criar_post.php" method="POST" enctype="multipart/form-data">
        <textarea name="texto" placeholder="O que você está praticando?" required></textarea>
        <input type="file" name="foto" accept="image/*">
        <button type="submit">Postar</button>
    </form>
</div>

<div class="feed-toggle">
    <a href="home.php?feed=seguindo" class="btn <?= ($_GET['feed'] ?? '') == 'seguindo' ? 'active' : '' ?>">Seguindo</a>
    <a href="home.php?feed=para-voce" class="btn <?= ($_GET['feed'] ?? '') == 'para-voce' ? 'active' : '' ?>">Para Você</a>
    <br><br>
</div>

            <?php foreach ($postagens as $post): ?>
    <div class="post">
        <div class="post-header">
            <img src="login/uploads/<?= htmlspecialchars($post['foto_perfil']) ?>" alt="perfil" width="40" height="40" style="border-radius:50%; object-fit:cover;">
            <div>
                <h3><?= htmlspecialchars($post['nome_usuario']) ?></h3>
                <p><?= htmlspecialchars($post['nome']) ?></p>
            </div>
        </div>
        <div class="post-body">
            <p><?= htmlspecialchars($post['texto']) ?></p>
            <?php if (!empty($post['foto'])): ?>
    <img src="/login/uploads/<?= htmlspecialchars($post['foto']) ?>" alt="foto da postagem">
<?php endif; ?>

        </div>
        <div class="post-footer" data-id="<?= $post['idpostagem'] ?>">
    <div class="actions">
        
        <a href="#" class="like-btn" data-postagem="<?= $post['idpostagem'] ?>">
            <i class="fa-<?= isset($curtidasDoUsuario[$post['idpostagem']]) ? 'solid' : 'regular' ?> fa-heart" style="<?= isset($curtidasDoUsuario[$post['idpostagem']]) ? 'color: #e91e63;' : '' ?>"></i> 
        </a>
        <span class="like-count"><?= $post['curtidas'] ?? 0 ?></span> Likes
         <i class="fa-regular fa-comment comment-btn"></i>
    </div>
    



    <!-- Formulário de Comentário -->
<div class="comment-box">
    <form action="comentar.php" method="POST">
        <input type="text" name="comentario" placeholder="Escreva seu comentário..." class="comment-input" required>
        <input type="hidden" name="idpostagem" value="<?= $post['idpostagem'] ?>">
        <button type="submit">Comentar</button>
    </form>
</div>


    <!-- Exibindo os comentários -->
    <div class="comments-list">
    <?php 
    // Usa comentários já carregados em batch para evitar N+1 queries
    $comentarios = $comentariosPorPostagem[$post['idpostagem']] ?? [];
    foreach ($comentarios as $comentario):
    ?>
        <div class="comment">
            <img src="login/uploads/<?= htmlspecialchars($comentario['foto_perfil']) ?>" alt="Foto do usuário" width="30" height="30" style="border-radius: 50%; object-fit: cover;">
            <p><strong><?= htmlspecialchars($comentario['nome_usuario']) ?>:</strong> <?= htmlspecialchars($comentario['comentario']) ?></p>
        </div>
    <?php endforeach; ?>
</div>




</div>

            </div>
<?php endforeach; ?>
        </main>

        <!-- Lateral direita -->
        <aside class="rightbar">
    <h3>Sugestões</h3>
    <ul>
          <?php foreach($sugestoes as $user): ?>
            <li class="sugestao-item">
                <a href="perfil.php?id=<?= $user['idusuarios'] ?>" class="perfil-link">
                    <img src="login/uploads/<?= htmlspecialchars($user['foto_perfil']) ?>" alt="<?= htmlspecialchars($user['nome_usuario']) ?>" width="40" height="40">
                    <div class="user-info">
                        <span class="nome"><?= htmlspecialchars($user['nome']) ?></span>
                        <span class="nome_usuario">@<?= htmlspecialchars($user['nome_usuario']) ?></span>
                    </div>
                </a>
                <a href="seguir.php?idseguidor=<?= $user['idusuarios'] ?>" class="seguir-btn">Seguir</a>
            </li>
        <?php endforeach; ?>
    </ul>
    <a href="seguidores.php">Ver Mais</a>   
</aside>
    </div>
</body>
<script src="script.js"></script>


</html>
