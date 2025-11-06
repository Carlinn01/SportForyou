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

// Busca todos os usuários
$usuarios = [];
try {
    $sql = "SELECT idusuarios, nome, nome_usuario, email, nascimento, foto_perfil, 
            (SELECT COUNT(*) FROM postagens WHERE idusuario = usuarios.idusuarios) as total_posts,
            (SELECT COUNT(*) FROM stories WHERE idusuario = usuarios.idusuarios) as total_stories,
            (SELECT COUNT(*) FROM seguidores WHERE idusuario = usuarios.idusuarios) as total_seguidores,
            (SELECT COUNT(*) FROM seguidores WHERE idseguidor = usuarios.idusuarios) as total_seguindo,
            is_admin
            FROM usuarios
            ORDER BY idusuarios DESC";
    $stmt = $conexao->prepare($sql);
    $stmt->execute();
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erro ao buscar usuários: " . $e->getMessage());
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
    <title>Gerenciar Usuários - Admin</title>
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
                    <i class="fa-solid fa-users"></i>
                    <h1>Gerenciar Usuários</h1>
                </div>
                <a href="admin.php" class="btn-admin btn-admin-view" style="margin-top: 15px;">
                    <i class="fa-solid fa-arrow-left"></i>
                    Voltar ao Painel
                </a>
            </div>

            <!-- Tabela de Usuários -->
            <div class="config-card">
                <h2 class="card-title">Lista de Usuários (<?= count($usuarios) ?>)</h2>
                <div style="overflow-x: auto;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Foto</th>
                                <th>Nome</th>
                                <th>Usuário</th>
                                <th>Email</th>
                                <th>Posts</th>
                                <th>Stories</th>
                                <th>Seguidores</th>
                                <th>Admin</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($usuarios)): ?>
                                <tr>
                                    <td colspan="10" style="text-align: center; padding: 40px; color: #666;">
                                        Nenhum usuário encontrado.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <tr>
                                        <td><?= $usuario['idusuarios'] ?></td>
                                        <td>
                                            <?php if ($usuario['foto_perfil']): ?>
                                                <img src="../login/uploads/<?= htmlspecialchars($usuario['foto_perfil']) ?>" alt="Foto" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                            <?php else: ?>
                                                <div style="width: 40px; height: 40px; border-radius: 50%; background: #ddd; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fa-solid fa-user" style="color: #999;"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($usuario['nome']) ?></td>
                                        <td>@<?= htmlspecialchars($usuario['nome_usuario']) ?></td>
                                        <td><?= htmlspecialchars($usuario['email']) ?></td>
                                        <td><?= $usuario['total_posts'] ?? 0 ?></td>
                                        <td><?= $usuario['total_stories'] ?? 0 ?></td>
                                        <td><?= $usuario['total_seguidores'] ?? 0 ?></td>
                                        <td>
                                            <?php if ($usuario['is_admin'] ?? 0): ?>
                                                <span style="color: #dc3545; font-weight: 600;">
                                                    <i class="fa-solid fa-shield-halved"></i> Admin
                                                </span>
                                            <?php else: ?>
                                                <span style="color: #666;">Usuário</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                                <a href="admin_editar_perfil.php?id=<?= $usuario['idusuarios'] ?>" class="btn-admin btn-admin-edit" title="Editar Perfil">
                                                    <i class="fa-solid fa-edit"></i>
                                                    Editar
                                                </a>
                                                <a href="../actions/admin_deletar_usuario.php?id=<?= $usuario['idusuarios'] ?>&token=<?= urlencode($csrf_token) ?>" 
                                                   class="btn-admin btn-admin-delete" 
                                                   title="Deletar Usuário"
                                                   onclick="return confirm('Tem certeza que deseja deletar o usuário <?= htmlspecialchars($usuario['nome'], ENT_QUOTES) ?>? Esta ação não pode ser desfeita e deletará todos os posts, stories e dados relacionados.')">
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

