<?php
  include "login/incs/valida-sessao.php";
  require_once "login/src/ConexaoBD.php";
$pdo = ConexaoBD::conectar(); // cria a conexão

$sql = "SELECT * FROM postagens ORDER BY criado_em DESC";
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
                    <li><i class="fa-solid fa-star"></i> Favoritos</li>
                </ul>
          </nav>
            <div class="usuario">
                <i class="fa-solid fa-user-circle"></i>
                <span>@usuario</span>
            </div>
        </aside>

        <!-- Conteúdo principal -->
        <main class="feed">
            <header class="topbar">
                <input type="text" placeholder="Pesquise pessoas ou esportes">
                <div class="icons">
                    <i class="fa-regular fa-bell"></i>
                    <i class="fa-regular fa-envelope"></i>
                    <i class="fa-solid fa-gear"></i>
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
                <textarea placeholder="O que você está postando?"></textarea>
            </div>

            <?php foreach ($postagens as $post): ?>
                <div class="post">
                    <div class="post-header">
                        <img src="https://i.pravatar.cc/100" alt="perfil">
                        <div>
                            <h3>X_AE_A-13</h3>
                            <p>Product Designer, slothUI</p>
                        </div>
                    </div>
                    <div class="post-body">
                        <p><?= htmlspecialchars($post['texto']) ?></p>
                        <?php if (!empty($post['foto'])): ?>
                            <img src="uploads/<?= htmlspecialchars($post['foto']) ?>" alt="foto da postagem">
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
            <h3>Sugestão</h3>
            <ul>
                <li><span>Julia Smith</span> @juliasmith</li>
                <li><span>Vermillion D. Gray</span> @vermilliongray</li>
                <li><span>Mai Senpai</span> @maisenpai</li>
                <li><span>Saylor U. Twift</span> @saylor.twift</li>
                <li><span>Azunyan J. Wu</span> @azunyan4ever</li>
                <li><span>Osarck Babama</span> @obama21</li>
            </ul>
        </aside>
    </div>
</body>
</html>
