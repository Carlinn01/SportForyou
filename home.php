<?php
  include "login/incs/valida-sessao.php";
  require_once "login/src/ConexaoBD.php";
$pdo = ConexaoBD::conectar(); // cria a conexão

$sql = "SELECT * FROM postagens ORDER BY criado_em DESC";
$stmt = $pdo->query($sql);
$postagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

$idusuario_logado = $_SESSION['idusuarios'];

// Pega 5 usuários aleatórios que não sejam o usuário logado
$sql = "SELECT idusuarios, nome, nome_usuario, foto_perfil 
        FROM usuarios 
        WHERE idusuarios != ? 
        ORDER BY RAND() 
        LIMIT 5";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(1, $idusuario_logado);
$stmt->execute();
$sugestoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT p.*, u.nome, u.nome_usuario, u.foto_perfil
        FROM postagens p
        JOIN usuarios u ON p.idusuario = u.idusuarios
        ORDER BY p.criado_em DESC";
$stmt = $pdo->query($sql);
$postagens = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>SportForYou</title>
    <link rel="stylesheet" href="feed.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  
    <div class="container">
        <!-- Sidebar esquerda -->
        <aside class="sidebar">
            <div class="logo">
                <img src="/img/sport 1.png" alt="Logo SportForYou">
            </div>
            <nav>
                <ul>
                    <li><i class="fa-solid fa-house"></i> Feed</li>
                    <li><i class="fa-solid fa-gamepad"></i> Esportes</li>
                    <li><i class="fa-solid fa-calendar-days"></i> Eventos</li>
                    <li><i class="fa-solid fa-star"></i> Salvos</li>
                    <li><i class="fa-solid fa-gear"></i> Configurações</li>
                </ul>
          </nav>
            
        <div class="usuario">
            <a href="/meu_perfil.php">
        <img src="/login/uploads/<?= htmlspecialchars($_SESSION['foto_perfil']) ?>" alt="Foto de perfil" width="40" height="40" style="border-radius:50%; object-fit:cover;">
        <span>@<?= htmlspecialchars($_SESSION['nome_usuario']) ?></span>
        </a>
</div>

        </aside>

        <!-- Conteúdo principal -->
        <main class="feed">
            <header class="topbar">
        <div class="search-container">
    <input type="text" placeholder="Pesquise pessoas ou esportes">
    <button type="submit" class="botao"><i class="fa-solid fa-magnifying-glass"></i></button>
</div>


                <div class="icons">
                    <i class="fa-solid fa-message"></i>
                    <i class="fa-regular fa-bell"></i>
                </div>
            </header>

            <div class="stories">
                <?php for ($i = 0; $i < 8; $i++): ?>
                    <div class="story">
                        <img src="https://i.pravatar.cc/100?img=<?php echo $i+1; ?>" alt="story">
                    </div>
                <?php endfor; ?>
            </div>

            <div class="new-post">
    <form action="criar_post.php" method="POST" enctype="multipart/form-data">
        <textarea name="texto" placeholder="O que você está postando?" required></textarea>
        <input type="file" name="foto" accept="image/*">
        <button type="submit">Postar</button>
    </form>
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
        <div class="post-footer">
            <div class="actions">
                <i class="fa-regular fa-heart"></i> 12 Likes
                <i class="fa-regular fa-comment"></i> 25 Comments
                <i class="fa-regular fa-share-from-square"></i> 187 Share
            </div>
            <div class="comment-box">
                <input type="text" placeholder="Write your comment...">
                <i class="fa-regular fa-paper-plane"></i>
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
</aside>
    </div>
</body>
</html>
