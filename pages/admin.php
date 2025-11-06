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

// Busca estatísticas
$stats = [
    'total_usuarios' => 0,
    'total_posts' => 0,
    'total_stories' => 0,
    'total_erros' => 0
];

try {
    // Total de usuários
    $sql = "SELECT COUNT(*) FROM usuarios";
    $stmt = $conexao->query($sql);
    $stats['total_usuarios'] = $stmt->fetchColumn();
    
    // Total de posts
    $sql = "SELECT COUNT(*) FROM postagens";
    $stmt = $conexao->query($sql);
    $stats['total_posts'] = $stmt->fetchColumn();
    
    // Total de stories
    $sql = "SELECT COUNT(*) FROM stories";
    $stmt = $conexao->query($sql);
    $stats['total_stories'] = $stmt->fetchColumn();
    
    // Total de erros reportados
    // Função para verificar se tabela existe (se não existir, declara)
    if (!function_exists('tabelaExiste')) {
        function tabelaExiste($pdo, $tabela) {
            try {
                $sql = "SHOW TABLES LIKE '$tabela'";
                $stmt = $pdo->query($sql);
                return $stmt->rowCount() > 0;
            } catch (PDOException $e) {
                return false;
            }
        }
    }
    
    if (tabelaExiste($conexao, 'erros_reportados')) {
        $sql = "SELECT COUNT(*) FROM erros_reportados";
        $stmt = $conexao->query($sql);
        $stats['total_erros'] = $stmt->fetchColumn();
    }
} catch (PDOException $e) {
    error_log("Erro ao buscar estatísticas: " . $e->getMessage());
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
    <title>Painel de Administração - SportForYou</title>
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

            <!-- Header Admin -->
            <div class="config-card header-card admin-header">
                <div class="card-header-content">
                    <i class="fa-solid fa-shield-halved"></i>
                    <h1>Painel de Administração</h1>
                </div>
                <div class="admin-warning">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>Área restrita - Apenas administradores</span>
                </div>
            </div>

            <!-- Estatísticas -->
            <div class="admin-stats">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
                    <div class="stat-info">
                        <div class="stat-value"><?= $stats['total_usuarios'] ?></div>
                        <div class="stat-label">Usuários</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid fa-image"></i></div>
                    <div class="stat-info">
                        <div class="stat-value"><?= $stats['total_posts'] ?></div>
                        <div class="stat-label">Posts</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid fa-circle-play"></i></div>
                    <div class="stat-info">
                        <div class="stat-value"><?= $stats['total_stories'] ?></div>
                        <div class="stat-label">Stories</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid fa-bug"></i></div>
                    <div class="stat-info">
                        <div class="stat-value"><?= $stats['total_erros'] ?></div>
                        <div class="stat-label">Erros Reportados</div>
                    </div>
                </div>
            </div>

            <!-- Menu de Ações -->
            <div class="config-card">
                <h2 class="card-title">Ações Administrativas</h2>
                <div class="admin-actions">
                    <a href="admin_usuarios.php" class="admin-action-card">
                        <i class="fa-solid fa-users"></i>
                        <h3>Gerenciar Usuários</h3>
                        <p>Visualizar, editar e deletar usuários</p>
                    </a>
                    <a href="admin_posts.php" class="admin-action-card">
                        <i class="fa-solid fa-image"></i>
                        <h3>Gerenciar Posts</h3>
                        <p>Visualizar e deletar posts</p>
                    </a>
                    <a href="admin_stories.php" class="admin-action-card">
                        <i class="fa-solid fa-circle-play"></i>
                        <h3>Gerenciar Stories</h3>
                        <p>Visualizar e deletar stories</p>
                    </a>
                    <a href="admin_erros.php" class="admin-action-card">
                        <i class="fa-solid fa-bug"></i>
                        <h3>Erros Reportados</h3>
                        <p>Visualizar e gerenciar erros reportados</p>
                    </a>
                </div>
            </div>
        </main>
    </div>

    <script src="../assets/js/tema.js"></script>
    <script src="../assets/js/mobile-menu.js"></script>
</body>
</html>

