<?php
include("../login/incs/valida-admin.php");
require_once "../login/src/ConexaoBD.php";
require_once "../login/src/CSRF.php";

$idusuario_logado = $_SESSION['idusuarios'];
$conexao = ConexaoBD::conectar();
$csrf_token = CSRF::gerarToken();

// Garante que a conexão usa UTF-8
try {
    $conexao->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
} catch (PDOException $e) {
    // Ignora se falhar
}

// Busca todos os stories
$stories = [];
try {
    $sql = "SELECT s.*, u.nome, u.nome_usuario, u.foto_perfil
            FROM stories s
            JOIN usuarios u ON s.idusuario = u.idusuarios
            ORDER BY s.criado_em DESC
            LIMIT 100";
    $stmt = $conexao->prepare($sql);
    $stmt->execute();
    $stories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erro ao buscar stories: " . $e->getMessage());
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
    <title>Gerenciar Stories - Admin</title>
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
                    <i class="fa-solid fa-circle-play"></i>
                    <h1>Gerenciar Stories</h1>
                </div>
                <a href="admin.php" class="btn-admin btn-admin-view" style="margin-top: 15px;">
                    <i class="fa-solid fa-arrow-left"></i>
                    Voltar ao Painel
                </a>
            </div>

            <!-- Lista de Stories -->
            <div class="config-card">
                <h2 class="card-title">Lista de Stories (<?= count($stories) ?>)</h2>
                <div style="overflow-x: auto;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Autor</th>
                                <th>Mídia</th>
                                <th>Tipo</th>
                                <th>Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($stories)): ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 40px; color: #666;">
                                        Nenhum story encontrado.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($stories as $story): 
                                    $dataFormatada = date('d/m/Y H:i', strtotime($story['criado_em']));
                                    $caminhoMidia = '../login/' . $story['midia'];
                                ?>
                                    <tr>
                                        <td><?= $story['idstory'] ?></td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 8px;">
                                                <?php if ($story['foto_perfil']): ?>
                                                    <img src="../login/uploads/<?= htmlspecialchars($story['foto_perfil']) ?>" alt="Foto" style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover;">
                                                <?php endif; ?>
                                                <div>
                                                    <div style="font-weight: 600;"><?= htmlspecialchars($story['nome']) ?></div>
                                                    <div style="font-size: 12px; color: #666;">@<?= htmlspecialchars($story['nome_usuario']) ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($story['tipo'] == 'imagem'): ?>
                                                <img src="<?= $caminhoMidia ?>" alt="Story" style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px;" onerror="this.src='../assets/img/placeholder.png'">
                                            <?php else: ?>
                                                <video src="<?= $caminhoMidia ?>" style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px;" onerror="this.style.display='none'"></video>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span style="text-transform: capitalize;"><?= htmlspecialchars($story['tipo']) ?></span>
                                        </td>
                                        <td><?= $dataFormatada ?></td>
                                        <td>
                                            <a href="../actions/admin_deletar_story.php?id=<?= $story['idstory'] ?>&token=<?= urlencode($csrf_token) ?>" 
                                               class="btn-admin btn-admin-delete" 
                                               title="Deletar Story"
                                               onclick="return confirm('Tem certeza que deseja deletar este story? Esta ação não pode ser desfeita.')">
                                                <i class="fa-solid fa-trash"></i>
                                                Deletar
                                            </a>
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

