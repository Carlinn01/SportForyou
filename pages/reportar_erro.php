<?php
include("../login/incs/valida-sessao.php");
require_once "../login/src/ConexaoBD.php";

$idusuario_logado = $_SESSION['idusuarios'];

// Verifica mensagens de sucesso/erro
$mensagem = isset($_SESSION['msg']) ? $_SESSION['msg'] : '';
$msg_tipo = isset($_SESSION['msg_tipo']) ? $_SESSION['msg_tipo'] : '';
// Limpa as mensagens da sessão após exibir
unset($_SESSION['msg']);
unset($_SESSION['msg_tipo']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportar Erro - SportForYou</title>
    <link rel="stylesheet" href="../assets/css/feed.css">
    <link rel="stylesheet" href="../assets/css/configuracoes.css">
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
                    <li class="<?= $paginaAtual == 'reportar_erro.php' ? 'ativo' : '' ?>"><a href="reportar_erro.php"><i class="fa-solid fa-bug"></i> Reportar Erro</a></li>
                    <?php
                    // Verifica se o usuário é admin
                    $is_admin = false;
                    try {
                        $conexaoAdmin = ConexaoBD::conectar();
                        $sqlAdmin = "SELECT is_admin FROM usuarios WHERE idusuarios = ?";
                        $stmtAdmin = $conexaoAdmin->prepare($sqlAdmin);
                        $stmtAdmin->execute([$idusuario_logado]);
                        $resultadoAdmin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);
                        $is_admin = $resultadoAdmin && $resultadoAdmin['is_admin'];
                    } catch (PDOException $e) {
                        // Ignora erro
                    }
                    if ($is_admin): ?>
                        <li class="<?= $paginaAtual == 'admin.php' ? 'ativo' : '' ?>"><a href="admin.php"><i class="fa-solid fa-shield-halved"></i> Admin</a></li>
                    <?php endif; ?>
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
        <main class="configuracoes-main">
            <!-- Mensagens de Sucesso/Erro -->
            <?php if ($mensagem): ?>
                <div class="alert alert-<?= $msg_tipo === 'sucesso' ? 'success' : ($msg_tipo === 'erro' ? 'error' : 'info') ?>" style="margin-bottom: 20px; padding: 15px; border-radius: 8px; background: <?= $msg_tipo === 'sucesso' ? '#d4edda' : ($msg_tipo === 'erro' ? '#f8d7da' : '#d1ecf1') ?>; color: <?= $msg_tipo === 'sucesso' ? '#155724' : ($msg_tipo === 'erro' ? '#721c24' : '#0c5460') ?>; border: 1px solid <?= $msg_tipo === 'sucesso' ? '#c3e6cb' : ($msg_tipo === 'erro' ? '#f5c6cb' : '#bee5eb') ?>;">
                    <?= htmlspecialchars($mensagem) ?>
                </div>
            <?php endif; ?>

            <!-- Card Header Reportar Erro -->
            <div class="config-card header-card">
                <div class="card-header-content">
                    <i class="fa-solid fa-bug"></i>
                    <h1>Reportar Erro</h1>
                </div>
            </div>

            <!-- Card Formulário de Reporte -->
            <div class="config-card">
                <h2 class="card-title">Descreva o problema</h2>
                <p style="color: #666; margin-bottom: 20px; font-size: 14px;">
                    Encontrou um bug ou problema? Nos ajude a melhorar reportando o erro. Sua contribuição é muito importante!
                </p>
                <form method="POST" action="../actions/reportar_erro.php" class="reportar-form" id="form-reportar">
                    <div class="form-group">
                        <label for="titulo">Título do Erro *</label>
                        <input type="text" id="titulo" name="titulo" required placeholder="Ex: Erro ao fazer login" maxlength="100">
                    </div>
                    <div class="form-group">
                        <label for="categoria">Categoria *</label>
                        <select id="categoria" name="categoria" required>
                            <option value="">Selecione uma categoria</option>
                            <option value="Bug">Bug</option>
                            <option value="Erro de Interface">Erro de Interface</option>
                            <option value="Problema de Performance">Problema de Performance</option>
                            <option value="Erro de Funcionalidade">Erro de Funcionalidade</option>
                            <option value="Outro">Outro</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="descricao">Descrição Detalhada *</label>
                        <textarea id="descricao" name="descricao" required rows="6" placeholder="Descreva o erro em detalhes. O que você estava fazendo quando o erro ocorreu? Qual mensagem apareceu? Passos para reproduzir o erro..." maxlength="1000"></textarea>
                        <small style="color: #999; font-size: 12px;">Máximo de 1000 caracteres</small>
                    </div>
                    <div class="form-group">
                        <label for="url_pagina">URL da Página (opcional)</label>
                        <input type="text" id="url_pagina" name="url_pagina" placeholder="Ex: /pages/home.php" maxlength="255">
                    </div>
                    <div class="form-actions">
                        <a href="configuracoes.php" class="btn-cancelar">
                            <i class="fa-solid fa-times"></i> Cancelar
                        </a>
                        <button type="submit" class="btn-salvar">
                            <i class="fa-solid fa-paper-plane"></i> Enviar Reporte
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script src="../assets/js/tema.js"></script>
    <script src="../assets/js/mobile-menu.js"></script>
    <script>
        // Adiciona a URL atual automaticamente
        document.addEventListener('DOMContentLoaded', function() {
            const urlInput = document.getElementById('url_pagina');
            if (urlInput && !urlInput.value) {
                urlInput.value = window.location.pathname;
            }
        });
    </script>
</body>
</html>

