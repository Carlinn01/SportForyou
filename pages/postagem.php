<?php
include("../login/incs/valida-sessao.php");
require_once "../login/src/ConexaoBD.php";
require_once "../login/src/PostagemDAO.php";
require_once "../login/src/ComentarioDAO.php";

if (!isset($_GET['id'])) {
    header("Location: home.php");
    exit;
}

$idpostagem = (int)$_GET['id'];
$idusuario_logado = $_SESSION['idusuarios'];

$conexao = ConexaoBD::conectar();

// Busca a postagem específica
$sql = "SELECT p.*, u.nome, u.nome_usuario, u.foto_perfil,
        (SELECT COUNT(*) FROM curtidas WHERE idpostagem = p.idpostagem) AS curtidas,
        (SELECT COUNT(*) FROM comentarios WHERE idpostagem = p.idpostagem) AS total_comentarios
        FROM postagens p
        JOIN usuarios u ON p.idusuario = u.idusuarios
        WHERE p.idpostagem = ?";
$stmt = $conexao->prepare($sql);
$stmt->execute([$idpostagem]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    $_SESSION['msg'] = 'Postagem não encontrada!';
    $_SESSION['msg_tipo'] = 'erro';
    header("Location: home.php");
    exit;
}

// Verifica se o usuário logado já curtiu esta postagem
$sqlCurtiu = "SELECT COUNT(*) FROM curtidas WHERE idusuario = ? AND idpostagem = ?";
$stmt = $conexao->prepare($sqlCurtiu);
$stmt->execute([$idusuario_logado, $idpostagem]);
$jaCurtiu = $stmt->fetchColumn() > 0;

// Busca todos os comentários desta postagem
$comentarios = ComentarioDAO::listarComentarios($idpostagem);

// Formata data
$dataPostagem = $post['criado_em'] ? date('d/m/Y H:i', strtotime($post['criado_em'])) : '';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postagem - SportForYou</title>
    <link rel="stylesheet" href="../assets/css/feed.css">
    <link rel="stylesheet" href="../assets/css/tema-escuro.css">
    <link rel="stylesheet" href="../assets/css/responsivo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar esquerda -->
        <aside class="sidebar" id="sidebar">
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
            <div class="postagem-container">
                <a href="home.php" class="btn-voltar">
                    <i class="fa-solid fa-arrow-left"></i> Voltar para o Feed
                </a>

                <div class="post">
                    <div class="post-header">
                        <img src="../login/uploads/<?= htmlspecialchars($post['foto_perfil']) ?>" alt="perfil" width="40" height="40" style="border-radius:50%; object-fit:cover;">
                        <div>
                            <h3><?= htmlspecialchars($post['nome_usuario']) ?></h3>
                            <p><?= htmlspecialchars($post['nome']) ?></p>
                        </div>
                        <small style="margin-left: auto; color: #888; font-size: 0.8em;"><?= htmlspecialchars($dataPostagem) ?></small>
                    </div>

                    <?php if ($post['texto']): ?>
                        <div class="post-body">
                            <p><?= nl2br(htmlspecialchars($post['texto'])) ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if ($post['foto']): ?>
                        <div class="post-body">
                            <img src="../login/uploads/<?= htmlspecialchars($post['foto']) ?>" alt="foto da postagem" style="width: 100%; border-radius: 10px;">
                        </div>
                    <?php endif; ?>

                    <div class="post-footer" data-id="<?= $post['idpostagem'] ?>">
                        <div class="actions">
                            <div class="like-wrapper">
                                <a href="#" class="like-btn" data-postagem="<?= $post['idpostagem'] ?>">
                                    <i class="fa-<?= $jaCurtiu ? 'solid' : 'regular' ?> fa-heart" style="color: <?= $jaCurtiu ? '#e91e63' : '' ?>;"></i> 
                                </a>
                                <span class="like-count"><?= $post['curtidas'] ?? 0 ?></span>
                                <span class="likes-text">Likes</span>
                            </div>
                            <div class="comment-wrapper">
                                <i class="fa-regular fa-comment comment-btn"></i>
                                <span class="comment-count"><?= $post['total_comentarios'] ?? count($comentarios) ?></span>
                            </div>
                        </div>

                        <!-- Formulário de Comentário -->
                        <div class="comment-box">
                            <form action="../api/comentar.php" method="POST" class="comment-form">
                                <div class="comment-input-wrapper">
                                    <input type="text" name="comentario" placeholder="Escreva seu comentário..." class="comment-input" required>
                                    <button type="button" class="emoji-btn" title="Adicionar emoji">😊</button>
                                    <div class="emoji-picker hidden">
                                        <div class="emoji-grid">
                                            <span class="emoji-option" data-emoji="😀">😀</span>
                                            <span class="emoji-option" data-emoji="😃">😃</span>
                                            <span class="emoji-option" data-emoji="😄">😄</span>
                                            <span class="emoji-option" data-emoji="😁">😁</span>
                                            <span class="emoji-option" data-emoji="😆">😆</span>
                                            <span class="emoji-option" data-emoji="😅">😅</span>
                                            <span class="emoji-option" data-emoji="🤣">🤣</span>
                                            <span class="emoji-option" data-emoji="😂">😂</span>
                                            <span class="emoji-option" data-emoji="🙂">🙂</span>
                                            <span class="emoji-option" data-emoji="🙃">🙃</span>
                                            <span class="emoji-option" data-emoji="😉">😉</span>
                                            <span class="emoji-option" data-emoji="😊">😊</span>
                                            <span class="emoji-option" data-emoji="😇">😇</span>
                                            <span class="emoji-option" data-emoji="🥰">🥰</span>
                                            <span class="emoji-option" data-emoji="😍">😍</span>
                                            <span class="emoji-option" data-emoji="🤩">🤩</span>
                                            <span class="emoji-option" data-emoji="😘">😘</span>
                                            <span class="emoji-option" data-emoji="😋">😋</span>
                                            <span class="emoji-option" data-emoji="😛">😛</span>
                                            <span class="emoji-option" data-emoji="😜">😜</span>
                                            <span class="emoji-option" data-emoji="🤪">🤪</span>
                                            <span class="emoji-option" data-emoji="😝">😝</span>
                                            <span class="emoji-option" data-emoji="🤗">🤗</span>
                                            <span class="emoji-option" data-emoji="🤔">🤔</span>
                                            <span class="emoji-option" data-emoji="🤨">🤨</span>
                                            <span class="emoji-option" data-emoji="😐">😐</span>
                                            <span class="emoji-option" data-emoji="😏">😏</span>
                                            <span class="emoji-option" data-emoji="😒">😒</span>
                                            <span class="emoji-option" data-emoji="🙄">🙄</span>
                                            <span class="emoji-option" data-emoji="😌">😌</span>
                                            <span class="emoji-option" data-emoji="😔">😔</span>
                                            <span class="emoji-option" data-emoji="😴">😴</span>
                                            <span class="emoji-option" data-emoji="😷">😷</span>
                                            <span class="emoji-option" data-emoji="🤒">🤒</span>
                                            <span class="emoji-option" data-emoji="🤯">🤯</span>
                                            <span class="emoji-option" data-emoji="🤠">🤠</span>
                                            <span class="emoji-option" data-emoji="🥳">🥳</span>
                                            <span class="emoji-option" data-emoji="😎">😎</span>
                                            <span class="emoji-option" data-emoji="🤓">🤓</span>
                                            <span class="emoji-option" data-emoji="😕">😕</span>
                                            <span class="emoji-option" data-emoji="😟">😟</span>
                                            <span class="emoji-option" data-emoji="😮">😮</span>
                                            <span class="emoji-option" data-emoji="😲">😲</span>
                                            <span class="emoji-option" data-emoji="😳">😳</span>
                                            <span class="emoji-option" data-emoji="🥺">🥺</span>
                                            <span class="emoji-option" data-emoji="😨">😨</span>
                                            <span class="emoji-option" data-emoji="😰">😰</span>
                                            <span class="emoji-option" data-emoji="😢">😢</span>
                                            <span class="emoji-option" data-emoji="😭">😭</span>
                                            <span class="emoji-option" data-emoji="😱">😱</span>
                                            <span class="emoji-option" data-emoji="😤">😤</span>
                                            <span class="emoji-option" data-emoji="😡">😡</span>
                                            <span class="emoji-option" data-emoji="😠">😠</span>
                                            <span class="emoji-option" data-emoji="🤬">🤬</span>
                                            <span class="emoji-option" data-emoji="💀">💀</span>
                                            <span class="emoji-option" data-emoji="💩">💩</span>
                                            <span class="emoji-option" data-emoji="👍">👍</span>
                                            <span class="emoji-option" data-emoji="👎">👎</span>
                                            <span class="emoji-option" data-emoji="👊">👊</span>
                                            <span class="emoji-option" data-emoji="✊">✊</span>
                                            <span class="emoji-option" data-emoji="🤝">🤝</span>
                                            <span class="emoji-option" data-emoji="👏">👏</span>
                                            <span class="emoji-option" data-emoji="🙏">🙏</span>
                                            <span class="emoji-option" data-emoji="💪">💪</span>
                                            <span class="emoji-option" data-emoji="❤️">❤️</span>
                                            <span class="emoji-option" data-emoji="🧡">🧡</span>
                                            <span class="emoji-option" data-emoji="💛">💛</span>
                                            <span class="emoji-option" data-emoji="💚">💚</span>
                                            <span class="emoji-option" data-emoji="💙">💙</span>
                                            <span class="emoji-option" data-emoji="💜">💜</span>
                                            <span class="emoji-option" data-emoji="🖤">🖤</span>
                                            <span class="emoji-option" data-emoji="🤍">🤍</span>
                                            <span class="emoji-option" data-emoji="💔">💔</span>
                                            <span class="emoji-option" data-emoji="💕">💕</span>
                                            <span class="emoji-option" data-emoji="🔥">🔥</span>
                                            <span class="emoji-option" data-emoji="💯">💯</span>
                                            <span class="emoji-option" data-emoji="✅">✅</span>
                                            <span class="emoji-option" data-emoji="⭐">⭐</span>
                                            <span class="emoji-option" data-emoji="🌟">🌟</span>
                                            <span class="emoji-option" data-emoji="⚡">⚡</span>
                                            <span class="emoji-option" data-emoji="⚽">⚽</span>
                                            <span class="emoji-option" data-emoji="🏀">🏀</span>
                                            <span class="emoji-option" data-emoji="🏈">🏈</span>
                                            <span class="emoji-option" data-emoji="⚾">⚾</span>
                                            <span class="emoji-option" data-emoji="🎾">🎾</span>
                                            <span class="emoji-option" data-emoji="🏐">🏐</span>
                                            <span class="emoji-option" data-emoji="🏆">🏆</span>
                                            <span class="emoji-option" data-emoji="🥇">🥇</span>
                                            <span class="emoji-option" data-emoji="🥈">🥈</span>
                                            <span class="emoji-option" data-emoji="🥉">🥉</span>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="idpostagem" value="<?= $post['idpostagem'] ?>">
                                <button type="submit">Comentar</button>
                            </form>
                        </div>

                        <!-- Exibindo os comentários -->
                        <div class="comments-list">
                            <?php if (empty($comentarios)): ?>
                                <p style="text-align: center; color: #999; padding: 20px;">Nenhum comentário ainda. Seja o primeiro a comentar!</p>
                            <?php else: ?>
                                <?php foreach ($comentarios as $comentario): ?>
                                    <div class="comment">
                                        <img src="../login/uploads/<?= htmlspecialchars($comentario['foto_perfil']) ?>" alt="Foto do usuário" width="30" height="30" style="border-radius: 50%; object-fit: cover;">
                                        <p><strong><?= htmlspecialchars($comentario['nome_usuario']) ?>:</strong> <?= htmlspecialchars($comentario['comentario']) ?></p>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../assets/js/script.js"></script>
    <script>
        // Inicializa funcionalidades de comentários e likes
        document.addEventListener('DOMContentLoaded', function() {
            // EMOJI PICKER
            document.querySelectorAll('.emoji-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const picker = this.nextElementSibling;
                    if (picker) {
                        picker.classList.toggle('hidden');
                    }
                });
            });

            document.querySelectorAll('.emoji-option').forEach(option => {
                option.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const emoji = this.dataset.emoji;
                    const form = this.closest('.comment-form');
                    const input = form.querySelector('.comment-input');
                    if (input) {
                        input.value += emoji;
                        input.focus();
                    }
                    const picker = this.closest('.emoji-picker');
                    if (picker) {
                        picker.classList.add('hidden');
                    }
                });
            });

            // Fecha emoji picker ao clicar fora
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.comment-input-wrapper')) {
                    document.querySelectorAll('.emoji-picker').forEach(picker => {
                        picker.classList.add('hidden');
                    });
                }
            });
        });

        // Toggle Sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('fechada');
            localStorage.setItem('sidebarFechada', sidebar.classList.contains('fechada'));
        }

        // Restaura estado da sidebar
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarFechada = localStorage.getItem('sidebarFechada') === 'true';
            if (sidebarFechada) {
                document.getElementById('sidebar').classList.add('fechada');
            }
        });
    </script>
    <script src="../assets/js/tema.js"></script>
    <script src="../assets/js/mobile-menu.js"></script>
</body>
</html>

