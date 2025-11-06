<?php
include("../login/incs/valida-admin.php");
require_once "../login/src/ConexaoBD.php";

$idusuario_logado = $_SESSION['idusuarios'];
$conexao = ConexaoBD::conectar();

// Garante que a conexão usa UTF-8
try {
    $conexao->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
} catch (PDOException $e) {
    // Ignora se falhar
}

// Busca todos os posts
$posts = [];
try {
    $sql = "SELECT p.*, u.nome, u.nome_usuario, u.foto_perfil,
            (SELECT COUNT(*) FROM curtidas WHERE idpostagem = p.idpostagem) as total_curtidas,
            (SELECT COUNT(*) FROM comentarios WHERE idpostagem = p.idpostagem) as total_comentarios
            FROM postagens p
            JOIN usuarios u ON p.idusuario = u.idusuarios
            ORDER BY p.criado_em DESC
            LIMIT 100";
    $stmt = $conexao->prepare($sql);
    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erro ao buscar posts: " . $e->getMessage());
}

// Verifica mensagens
$mensagem = isset($_SESSION['msg']) ? $_SESSION['msg'] : '';
$msg_tipo = isset($_SESSION['msg_tipo']) ? $_SESSION['msg_tipo'] : '';
unset($_SESSION['msg']);
unset($_SESSION['msg_tipo']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Posts - Admin</title>
    <link rel="stylesheet" href="../assets/css/feed.css">
    <link rel="stylesheet" href="../assets/css/configuracoes.css">
    <link rel="stylesheet" href="../assets/css/tema-escuro.css">
    <link rel="stylesheet" href="../assets/css/responsivo.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
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
                    <li class="<?= $paginaAtual == 'admin.php' ? 'ativo' : '' ?>"><a href="admin.php"><i class="fa-solid fa-shield-halved"></i> Admin</a></li>
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
        <main class="configuracoes-main admin-main">
            <!-- Mensagens -->
            <?php if ($mensagem): ?>
                <div class="alert alert-<?= $msg_tipo === 'sucesso' ? 'success' : ($msg_tipo === 'erro' ? 'error' : 'info') ?>" style="margin-bottom: 20px; padding: 15px; border-radius: 8px; background: <?= $msg_tipo === 'sucesso' ? '#d4edda' : ($msg_tipo === 'erro' ? '#f8d7da' : '#d1ecf1') ?>; color: <?= $msg_tipo === 'sucesso' ? '#155724' : ($msg_tipo === 'erro' ? '#721c24' : '#0c5460') ?>; border: 1px solid <?= $msg_tipo === 'sucesso' ? '#c3e6cb' : ($msg_tipo === 'erro' ? '#f5c6cb' : '#bee5eb') ?>;">
                    <?= htmlspecialchars($mensagem) ?>
                </div>
            <?php endif; ?>

            <!-- Header -->
            <div class="config-card header-card">
                <div class="card-header-content">
                    <i class="fa-solid fa-image"></i>
                    <h1>Gerenciar Posts</h1>
                </div>
                <a href="admin.php" class="btn-admin btn-admin-view" style="margin-top: 15px;">
                    <i class="fa-solid fa-arrow-left"></i>
                    Voltar ao Painel
                </a>
            </div>

            <!-- Lista de Posts -->
            <div class="config-card">
                <h2 class="card-title">Lista de Posts (<?= count($posts) ?>)</h2>
                <div style="overflow-x: auto;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Autor</th>
                                <th>Conteúdo</th>
                                <th>Foto</th>
                                <th>Curtidas</th>
                                <th>Comentários</th>
                                <th>Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($posts)): ?>
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 40px; color: #666;">
                                        Nenhum post encontrado.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($posts as $post): 
                                    $dataFormatada = date('d/m/Y H:i', strtotime($post['criado_em']));
                                    $texto = $post['texto'] ?? '';
                                    $textoPreview = function_exists('mb_substr') ? mb_substr($texto, 0, 50) : substr($texto, 0, 50);
                                    if (strlen($texto) > 50) $textoPreview .= '...';
                                ?>
                                    <tr>
                                        <td><?= $post['idpostagem'] ?></td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 8px;">
                                                <?php if ($post['foto_perfil']): ?>
                                                    <img src="../login/uploads/<?= htmlspecialchars($post['foto_perfil']) ?>" alt="Foto" style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover;">
                                                <?php endif; ?>
                                                <div>
                                                    <div style="font-weight: 600;"><?= htmlspecialchars($post['nome']) ?></div>
                                                    <div style="font-size: 12px; color: #666;">@<?= htmlspecialchars($post['nome_usuario']) ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($textoPreview) ?></td>
                                        <td>
                                            <?php if ($post['foto']): ?>
                                                <img src="../login/uploads/<?= htmlspecialchars($post['foto']) ?>" alt="Post" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                            <?php else: ?>
                                                <span style="color: #999;">Sem foto</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $post['total_curtidas'] ?? 0 ?></td>
                                        <td><?= $post['total_comentarios'] ?? 0 ?></td>
                                        <td><?= $dataFormatada ?></td>
                                        <td>
                                            <div style="display: flex; gap: 8px;">
                                                <a href="postagem.php?id=<?= $post['idpostagem'] ?>" class="btn-admin btn-admin-view" title="Ver Post">
                                                    <i class="fa-solid fa-eye"></i>
                                                    Ver
                                                </a>
                                                <a href="../actions/admin_deletar_post.php?id=<?= $post['idpostagem'] ?>" 
                                                   class="btn-admin btn-admin-delete" 
                                                   title="Deletar Post"
                                                   onclick="return confirm('Tem certeza que deseja deletar este post? Esta ação não pode ser desfeita.')">
                                                    <i class="fa-solid fa-trash"></i>
                                                    Deletar
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script src="../assets/js/tema.js"></script>
    <script src="../assets/js/mobile-menu.js"></script>
</body>
</html>

