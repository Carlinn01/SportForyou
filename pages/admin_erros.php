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

// Função para verificar se a tabela existe
function tabelaExiste($pdo, $tabela) {
    try {
        $sql = "SHOW TABLES LIKE '$tabela'";
        $stmt = $pdo->query($sql);
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        return false;
    }
}

// Busca erros reportados
$erros = [];
if (tabelaExiste($conexao, 'erros_reportados')) {
    try {
        $sql = "SELECT e.*, u.nome, u.nome_usuario, u.email
                FROM erros_reportados e
                JOIN usuarios u ON e.idusuario = u.idusuarios
                ORDER BY e.criado_em DESC
                LIMIT 100";
        $stmt = $conexao->prepare($sql);
        $stmt->execute();
        $erros = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erro ao buscar erros: " . $e->getMessage());
    }
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
    <title>Erros Reportados - Admin</title>
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
                    <i class="fa-solid fa-bug"></i>
                    <h1>Erros Reportados</h1>
                </div>
                <a href="admin.php" class="btn-admin btn-admin-view" style="margin-top: 15px;">
                    <i class="fa-solid fa-arrow-left"></i>
                    Voltar ao Painel
                </a>
            </div>

            <!-- Lista de Erros -->
            <?php if (empty($erros)): ?>
                <div class="config-card">
                    <p style="text-align: center; color: #666; padding: 40px;">
                        <i class="fa-solid fa-inbox" style="font-size: 48px; color: #ccc; margin-bottom: 15px; display: block;"></i>
                        Nenhum erro reportado ainda.
                    </p>
                </div>
            <?php else: ?>
                <?php foreach ($erros as $erro): 
                    $dataFormatada = date('d/m/Y H:i', strtotime($erro['criado_em']));
                    $statusClass = '';
                    $statusTexto = '';
                    switch($erro['status']) {
                        case 'pendente':
                            $statusClass = 'status-pendente';
                            $statusTexto = 'Pendente';
                            break;
                        case 'em_analise':
                            $statusClass = 'status-analise';
                            $statusTexto = 'Em Análise';
                            break;
                        case 'resolvido':
                            $statusClass = 'status-resolvido';
                            $statusTexto = 'Resolvido';
                            break;
                        case 'descartado':
                            $statusClass = 'status-descartado';
                            $statusTexto = 'Descartado';
                            break;
                    }
                ?>
                    <div class="config-card erro-card">
                        <div class="erro-header">
                            <div class="erro-info">
                                <h3 class="erro-titulo"><?= htmlspecialchars($erro['titulo']) ?></h3>
                                <div class="erro-meta">
                                    <span class="erro-usuario">
                                        <i class="fa-solid fa-user"></i>
                                        <?= htmlspecialchars($erro['nome']) ?> (@<?= htmlspecialchars($erro['nome_usuario']) ?>)
                                    </span>
                                    <span class="erro-data">
                                        <i class="fa-solid fa-clock"></i>
                                        <?= $dataFormatada ?>
                                    </span>
                                </div>
                            </div>
                            <div class="erro-actions">
                                <span class="status-badge <?= $statusClass ?>"><?= $statusTexto ?></span>
                                <form method="POST" action="../actions/atualizar_status_erro.php" style="display: inline-block;">
                                    <input type="hidden" name="erro_id" value="<?= $erro['id'] ?>">
                                    <select name="novo_status" class="status-select" onchange="this.form.submit()">
                                        <option value="pendente" <?= $erro['status'] == 'pendente' ? 'selected' : '' ?>>Pendente</option>
                                        <option value="em_analise" <?= $erro['status'] == 'em_analise' ? 'selected' : '' ?>>Em Análise</option>
                                        <option value="resolvido" <?= $erro['status'] == 'resolvido' ? 'selected' : '' ?>>Resolvido</option>
                                        <option value="descartado" <?= $erro['status'] == 'descartado' ? 'selected' : '' ?>>Descartado</option>
                                    </select>
                                </form>
                            </div>
                        </div>
                        <div class="erro-body">
                            <div class="erro-categoria">
                                <i class="fa-solid fa-tag"></i>
                                <strong>Categoria:</strong> <?= htmlspecialchars($erro['categoria']) ?>
                            </div>
                            <?php if ($erro['url_pagina']): ?>
                                <div class="erro-url">
                                    <i class="fa-solid fa-link"></i>
                                    <strong>URL:</strong> <code><?= htmlspecialchars($erro['url_pagina']) ?></code>
                                </div>
                            <?php endif; ?>
                            <div class="erro-descricao">
                                <strong>Descrição:</strong>
                                <p><?= nl2br(htmlspecialchars($erro['descricao'])) ?></p>
                            </div>
                            <div class="erro-contato">
                                <i class="fa-solid fa-envelope"></i>
                                <strong>Contato:</strong> <?= htmlspecialchars($erro['email']) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </main>
    </div>

    <script src="../assets/js/tema.js"></script>
    <script src="../assets/js/mobile-menu.js"></script>
</body>
</html>

