<?php
include("../login/incs/valida-sessao.php");
require_once "../login/src/ConexaoBD.php";
require_once "../login/src/UsuarioDAO.php";
require_once "../login/src/CSRF.php";

$idusuario_logado = $_SESSION['idusuarios'];
$conexao = ConexaoBD::conectar();
$csrf_token = CSRF::gerarToken();

// Busca todas as conversas do usuário logado
$sql = "SELECT c.*, 
        CASE 
            WHEN c.usuario1_id = ? THEN u2.idusuarios
            ELSE u1.idusuarios
        END as outro_usuario_id,
        CASE 
            WHEN c.usuario1_id = ? THEN u2.nome
            ELSE u1.nome
        END as outro_usuario_nome,
        CASE 
            WHEN c.usuario1_id = ? THEN u2.nome_usuario
            ELSE u1.nome_usuario
        END as outro_usuario_nome_usuario,
        CASE 
            WHEN c.usuario1_id = ? THEN u2.foto_perfil
            ELSE u1.foto_perfil
        END as outro_usuario_foto,
        m.conteudo as ultima_mensagem,
        m.criado_em as ultima_mensagem_data,
        m.remetente_id as ultimo_remetente_id,
        (SELECT COUNT(*) 
         FROM mensagens m2
         LEFT JOIN mensagens_lidas ml ON m2.idmensagem = ml.mensagem_id AND ml.usuario_id = ?
         WHERE m2.conversa_id = c.idconversa 
         AND m2.remetente_id != ?
         AND ml.id IS NULL) as nao_lidas
        FROM conversas c
        LEFT JOIN usuarios u1 ON c.usuario1_id = u1.idusuarios
        LEFT JOIN usuarios u2 ON c.usuario2_id = u2.idusuarios
        LEFT JOIN mensagens m ON c.ultima_mensagem_id = m.idmensagem
        WHERE (c.usuario1_id = ? OR c.usuario2_id = ?)
        ORDER BY COALESCE(m.criado_em, c.atualizado_em) DESC";
        
$stmt = $conexao->prepare($sql);
$stmt->execute([$idusuario_logado, $idusuario_logado, $idusuario_logado, $idusuario_logado, $idusuario_logado, $idusuario_logado, $idusuario_logado, $idusuario_logado]);
$conversas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Busca sugestões de usuários para conversar (exclui usuários que já têm conversas)
$sugestoes = UsuarioDAO::listarSugestoes($idusuario_logado, 10, true);

// Se há uma conversa selecionada
$conversa_selecionada = null;
$mensagens = [];
$outro_usuario_chat = null;

if (isset($_GET['conversa'])) {
    $idconversa = (int)$_GET['conversa'];
    
    // Busca a conversa
    $sqlConversa = "SELECT c.*, 
                    CASE 
                        WHEN c.usuario1_id = ? THEN u2.idusuarios
                        ELSE u1.idusuarios
                    END as outro_usuario_id,
                    CASE 
                        WHEN c.usuario1_id = ? THEN u2.nome
                        ELSE u1.nome
                    END as outro_usuario_nome,
                    CASE 
                        WHEN c.usuario1_id = ? THEN u2.nome_usuario
                        ELSE u1.nome_usuario
                    END as outro_usuario_nome_usuario,
                    CASE 
                        WHEN c.usuario1_id = ? THEN u2.foto_perfil
                        ELSE u1.foto_perfil
                    END as outro_usuario_foto
                    FROM conversas c
                    LEFT JOIN usuarios u1 ON c.usuario1_id = u1.idusuarios
                    LEFT JOIN usuarios u2 ON c.usuario2_id = u2.idusuarios
                    WHERE c.idconversa = ? 
                    AND (c.usuario1_id = ? OR c.usuario2_id = ?)";
    
    $stmt = $conexao->prepare($sqlConversa);
    $stmt->execute([$idusuario_logado, $idusuario_logado, $idusuario_logado, $idusuario_logado, $idconversa, $idusuario_logado, $idusuario_logado]);
    $conversa_selecionada = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($conversa_selecionada) {
        $outro_usuario_chat = [
            'id' => $conversa_selecionada['outro_usuario_id'],
            'nome' => $conversa_selecionada['outro_usuario_nome'],
            'nome_usuario' => $conversa_selecionada['outro_usuario_nome_usuario'],
            'foto_perfil' => $conversa_selecionada['outro_usuario_foto']
        ];
        
        // Busca mensagens da conversa
        $sqlMensagens = "SELECT m.*, u.nome, u.nome_usuario, u.foto_perfil, m.anexo_url
                        FROM mensagens m
                        JOIN usuarios u ON m.remetente_id = u.idusuarios
                        WHERE m.conversa_id = ?
                        ORDER BY m.criado_em ASC";
        $stmt = $conexao->prepare($sqlMensagens);
        $stmt->execute([$idconversa]);
        $mensagens = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Marca mensagens como lidas
        $sqlMarcaComoLida = "INSERT IGNORE INTO mensagens_lidas (mensagem_id, usuario_id)
                            SELECT idmensagem, ? 
                            FROM mensagens 
                            WHERE conversa_id = ? AND remetente_id != ?";
        $stmt = $conexao->prepare($sqlMarcaComoLida);
        $stmt->execute([$idusuario_logado, $idconversa, $idusuario_logado]);
        
        // Atualiza status das mensagens recebidas como "lida"
        $sqlUpdateStatus = "UPDATE mensagens SET status = 'lida' 
                           WHERE conversa_id = ? AND remetente_id != ? AND status != 'lida'";
        $stmt = $conexao->prepare($sqlUpdateStatus);
        $stmt->execute([$idconversa, $idusuario_logado]);
        
        // Busca novamente as mensagens para ter o status atualizado
        $stmt = $conexao->prepare($sqlMensagens);
        $stmt->execute([$idconversa]);
        $mensagens = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensagens - SportForYou</title>
    <?php if ($conversa_selecionada && $outro_usuario_chat && isset($_GET['conversa'])): ?>
    <style>
        /* CSS inline crítico para forçar abertura do chat no mobile - MÁXIMA PRIORIDADE */
        @media screen and (max-width: 768px) {
            /* Seletores com máxima especificidade para garantir que sobrescrevam tudo */
            html body .mensagens-container .chat-panel.mobile-chat-active,
            html body .mensagens-container .chat-panel[data-mobile-active="true"],
            html body .chat-panel.mobile-chat-active,
            html body .chat-panel[data-mobile-active="true"],
            .chat-panel.mobile-chat-active,
            .chat-panel[data-mobile-active="true"] {
                transform: translateX(0) !important;
                -webkit-transform: translateX(0) !important;
                -moz-transform: translateX(0) !important;
                -ms-transform: translateX(0) !important;
                -o-transform: translateX(0) !important;
                display: flex !important;
                z-index: 1001 !important;
                position: absolute !important;
                right: 0 !important;
                top: 0 !important;
                width: 100% !important;
                height: 100vh !important;
                visibility: visible !important;
                opacity: 1 !important;
                overflow: visible !important;
                transition: none !important;
            }
            html body .mensagens-container .conversas-panel.mobile-conversas-hidden,
            html body .mensagens-container .conversas-panel[data-mobile-hidden="true"],
            html body .conversas-panel.mobile-conversas-hidden,
            html body .conversas-panel[data-mobile-hidden="true"],
            .conversas-panel.mobile-conversas-hidden,
            .conversas-panel[data-mobile-hidden="true"] {
                transform: translateX(-100%) !important;
                -webkit-transform: translateX(-100%) !important;
                -moz-transform: translateX(-100%) !important;
                -ms-transform: translateX(-100%) !important;
                -o-transform: translateX(-100%) !important;
                display: none !important;
                visibility: hidden !important;
                opacity: 0 !important;
            }
        }
    </style>
    <script>
        // Script crítico que executa ANTES de tudo - MÁXIMA PRIORIDADE
        (function() {
            var tentativas = 0;
            var maxTentativas = 30;
            
            function abrirChatAgora() {
                if (window.innerWidth <= 768) {
                    try {
                        var chat = document.querySelector('.chat-panel');
                        var conversas = document.getElementById('conversas-panel');
                        
                        if (chat) {
                            // Força o estilo com máxima prioridade
                            chat.setAttribute('style', 'transform: translateX(0) !important; -webkit-transform: translateX(0) !important; display: flex !important; z-index: 1001 !important; position: absolute !important; right: 0 !important; top: 0 !important; width: 100% !important; height: 100vh !important; visibility: visible !important; opacity: 1 !important;');
                            chat.classList.add('active', 'mobile-active');
                            chat.id = 'chat-panel-active';
                        }
                        
                        if (conversas) {
                            conversas.setAttribute('style', 'transform: translateX(-100%) !important; -webkit-transform: translateX(-100%) !important; display: none !important; visibility: hidden !important;');
                            conversas.classList.add('hidden');
                        }
                        
                        return chat !== null;
                    } catch(e) {
                        return false;
                    }
                }
                return true;
            }
            
            function tentarAbrir() {
                if (abrirChatAgora() || tentativas >= maxTentativas) {
                    return;
                }
                tentativas++;
                setTimeout(tentarAbrir, 20);
            }
            
            // Tenta imediatamente se o body existe
            if (document.body) {
                tentarAbrir();
            } else {
                // Aguarda o body
                var observerBody = new MutationObserver(function() {
                    if (document.body) {
                        tentarAbrir();
                        observerBody.disconnect();
                    }
                });
                observerBody.observe(document.documentElement, { childList: true, subtree: true });
            }
            
            // Também tenta quando o DOM carregar
            document.addEventListener('DOMContentLoaded', tentarAbrir);
            
            // Fallbacks adicionais
            setTimeout(tentarAbrir, 0);
            setTimeout(tentarAbrir, 10);
            setTimeout(tentarAbrir, 50);
            setTimeout(tentarAbrir, 100);
            setTimeout(tentarAbrir, 200);
        })();
    </script>
    <?php endif; ?>
    <link rel="stylesheet" href="../assets/css/feed.css">
    <link rel="stylesheet" href="../assets/css/mensagens.css">
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

        <!-- Área de Mensagens -->
        <main class="mensagens-container">
            <!-- Painel Esquerdo - Lista de Conversas -->
            <div class="conversas-panel <?php if ($conversa_selecionada && $outro_usuario_chat && isset($_GET['conversa'])): ?>mobile-conversas-hidden<?php endif; ?>" id="conversas-panel" <?php if ($conversa_selecionada && $outro_usuario_chat && isset($_GET['conversa'])): ?>data-mobile-hidden="true"<?php endif; ?>>
                <div class="conversas-header">
                    <i class="fa-solid fa-trophy"></i>
                    <h2>Mensagens</h2>
                </div>
                
                <div class="search-conversas">
                    <input type="text" id="pesquisa-conversas" placeholder="Pesquise pessoas...">
                </div>
                
                <div class="conversas-list" id="lista-conversas">
                    <?php if (empty($conversas)): ?>
                        <p class="sem-conversas">Nenhuma conversa ainda. Comece uma nova conversa!</p>
                    <?php else: ?>
                        <?php foreach ($conversas as $conv): 
                            $ativo = isset($_GET['conversa']) && $_GET['conversa'] == $conv['idconversa'];
                            
                            // Formata preview da última mensagem
                            $preview = '';
                            if ($conv['ultima_mensagem']) {
                                $preview = function_exists('mb_substr') 
                                    ? mb_substr($conv['ultima_mensagem'], 0, 30) 
                                    : substr($conv['ultima_mensagem'], 0, 30);
                                if (strlen($conv['ultima_mensagem']) > 30) $preview .= '...';
                            }
                            
                            // Verifica se a última mensagem é do outro usuário
                            $eh_do_outro = $conv['ultimo_remetente_id'] == $conv['outro_usuario_id'];
                            
                            // Pega o número de mensagens não lidas (já vem da query)
                            $naoLidas = (int)($conv['nao_lidas'] ?? 0);
                        ?>
                            <a href="mensagens.php?conversa=<?= $conv['idconversa'] ?>" class="conversa-item <?= $ativo ? 'ativo' : '' ?> <?= $naoLidas > 0 ? 'tem-nova-mensagem' : '' ?>">
                                <img src="../login/uploads/<?= htmlspecialchars($conv['outro_usuario_foto']) ?>" alt="<?= htmlspecialchars($conv['outro_usuario_nome_usuario']) ?>" class="conversa-avatar">
                                <div class="conversa-info">
                                    <div class="conversa-header-info">
                                        <span class="conversa-nome"><?= htmlspecialchars($conv['outro_usuario_nome']) ?></span>
                                        <?php if ($naoLidas > 0): ?>
                                            <span class="badge-nao-lidas"><?= $naoLidas ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($preview): ?>
                                        <span class="conversa-preview"><?= htmlspecialchars($preview) ?></span>
                                    <?php else: ?>
                                        <span class="conversa-preview">Nenhuma mensagem ainda</span>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <!-- Usuários sugeridos para conversar -->
                    <?php if (!empty($sugestoes)): ?>
                        <div class="sugestoes-header">
                            <h3>Novas Conversas</h3>
                        </div>
                        <?php foreach($sugestoes as $user): 
                            // Garante que não mostra o próprio usuário
                            if ($user['idusuarios'] == $idusuario_logado) continue;
                            $primeiroNome = explode(' ', $user['nome'])[0];
                        ?>
                            <a href="../actions/iniciar_conversa.php?id=<?= $user['idusuarios'] ?>" class="conversa-item">
                                <img src="../login/uploads/<?= htmlspecialchars($user['foto_perfil']) ?>" alt="<?= htmlspecialchars($user['nome_usuario']) ?>" class="conversa-avatar">
                                <div class="conversa-info">
                                    <span class="conversa-nome"><?= htmlspecialchars($primeiroNome) ?></span>
                                    <span class="conversa-preview">@<?= htmlspecialchars($user['nome_usuario']) ?></span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Painel Direito - Chat -->
            <div class="chat-panel <?php if ($conversa_selecionada && $outro_usuario_chat && isset($_GET['conversa'])): ?>mobile-chat-active<?php endif; ?>" <?php if ($conversa_selecionada && $outro_usuario_chat && isset($_GET['conversa'])): ?>data-mobile-active="true"<?php endif; ?>>
                <?php if ($conversa_selecionada && $outro_usuario_chat): ?>
                    <!-- Header do Chat -->
                    <div class="chat-header">
                        <button class="btn-voltar-chat-mobile" onclick="voltarParaConversas()" style="display: none;">
                            <i class="fa-solid fa-arrow-left"></i>
                        </button>
                        <img src="../login/uploads/<?= htmlspecialchars($outro_usuario_chat['foto_perfil']) ?>" alt="<?= htmlspecialchars($outro_usuario_chat['nome_usuario']) ?>" class="chat-header-avatar">
                        <div class="chat-header-info">
                            <span class="chat-header-nome"><?= htmlspecialchars($outro_usuario_chat['nome']) ?></span>
                            <span class="chat-header-usuario">@<?= htmlspecialchars($outro_usuario_chat['nome_usuario']) ?></span>
                        </div>
                    </div>

                    <!-- Área de Mensagens -->
                    <div class="mensagens-area" id="mensagens-area" data-conversa="<?= $conversa_selecionada['idconversa'] ?>">
                        <?php foreach ($mensagens as $msg): 
                            $eh_minha = $msg['remetente_id'] == $idusuario_logado;
                            $hora = date('H:i', strtotime($msg['criado_em']));
                        ?>
                            <div class="mensagem-item <?= $eh_minha ? 'minha-mensagem' : 'outra-mensagem' ?>" data-mensagem-id="<?= $msg['idmensagem'] ?>">
                                <?php if (!$eh_minha): ?>
                                    <img src="../login/uploads/<?= htmlspecialchars($msg['foto_perfil']) ?>" alt="<?= htmlspecialchars($msg['nome_usuario']) ?>" class="mensagem-avatar">
                                <?php endif; ?>
                                <div class="mensagem-bubble">
                                    <span class="mensagem-remetente"><?= htmlspecialchars($eh_minha ? 'Eu' : $msg['nome_usuario']) ?></span>
                                    <p class="mensagem-conteudo">
                                        <?= nl2br(htmlspecialchars($msg['conteudo'])) ?>
                                        <?php if (isset($msg['anexo_url']) && $msg['anexo_url']): 
                                            $anexo_url = $msg['anexo_url'];
                                            if (preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $anexo_url)): ?>
                                                <br><img src="../login/uploads/<?= htmlspecialchars($anexo_url) ?>" alt="Anexo" class="mensagem-anexo-imagem" onclick="window.open('../login/uploads/<?= htmlspecialchars($anexo_url) ?>', '_blank')">
                                            <?php elseif (preg_match('/\.(mp4|mov|quicktime)$/i', $anexo_url)): ?>
                                                <br><video controls class="mensagem-anexo-video"><source src="../login/uploads/<?= htmlspecialchars($anexo_url) ?>" type="video/mp4"></video>
                                            <?php else: 
                                                $nomeArquivo = basename($anexo_url); ?>
                                                <br><a href="../login/uploads/<?= htmlspecialchars($anexo_url) ?>" target="_blank" class="mensagem-anexo-link">📎 <?= htmlspecialchars($nomeArquivo) ?></a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <?php if ($eh_minha): 
                                    // Busca status atualizado da mensagem
                                    $sqlStatus = "SELECT status FROM mensagens WHERE idmensagem = ?";
                                    $stmtStatus = $conexao->prepare($sqlStatus);
                                    $stmtStatus->execute([$msg['idmensagem']]);
                                    $statusAtual = $stmtStatus->fetchColumn() ?: $msg['status'];
                                ?>
                                    <div class="mensagem-meta">
                                        <span class="mensagem-hora"><?= $hora ?></span>
                                        <?php if ($statusAtual == 'lida'): ?>
                                            <i class="fa-solid fa-check-double mensagem-lida"></i>
                                        <?php elseif ($statusAtual == 'entregue'): ?>
                                            <i class="fa-solid fa-check-double"></i>
                                        <?php else: ?>
                                            <i class="fa-solid fa-check"></i>
                                        <?php endif; ?>
                                    </div>
                                    <img src="../login/uploads/<?= htmlspecialchars($_SESSION['foto_perfil']) ?>" alt="Meu perfil" class="mensagem-avatar">
                                <?php else: ?>
                                    <div class="mensagem-meta">
                                        <span class="mensagem-hora"><?= $hora ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Input de Mensagem -->
                    <div class="mensagem-input-area">
                        <!-- Área para mostrar arquivo selecionado -->
                        <div id="arquivo-selecionado" class="arquivo-selecionado" style="display: none;">
                            <div class="arquivo-info">
                                <i class="fa-solid fa-file"></i>
                                <span id="nome-arquivo" class="nome-arquivo"></span>
                                <button type="button" class="btn-remover-arquivo" onclick="removerArquivo()" title="Remover arquivo">
                                    <i class="fa-solid fa-times"></i>
                                </button>
                            </div>
                        </div>
                               <form id="form-enviar-mensagem" method="POST" action="../api/enviar_mensagem.php" enctype="multipart/form-data">
                                   <?= CSRF::campoHidden() ?>
                                   <input type="hidden" name="conversa_id" value="<?= $conversa_selecionada['idconversa'] ?>">
                            <input type="file" id="input-anexo" name="anexo" accept="image/*,video/*,.pdf,.doc,.docx" style="display: none;">
                            <input type="text" name="mensagem" id="input-mensagem" placeholder="Digite sua mensagem..." autocomplete="off">
                            <div class="emoji-picker-container" id="emoji-picker-container">
                                <div class="emoji-picker" id="emoji-picker-mensagens">
                                    <?php 
                                    $emojis = ['😀', '😃', '😄', '😁', '😆', '😅', '😂', '🤣', '😊', '😇', '🙂', '🙃', '😉', '😌', '😍', '🥰', '😘', '😗', '😙', '😚', '😋', '😛', '😝', '😜', '🤪', '🤨', '🧐', '🤓', '😎', '🤩', '🥳', '😏', '😒', '😞', '😔', '😟', '😕', '🙁', '😣', '😖', '😫', '😩', '🥺', '😢', '😭', '😤', '😠', '😡', '🤬', '🤯', '😳', '🥵', '🥶', '😱', '😨', '😰', '😥', '😓', '🤗', '🤔', '🤭', '🤫', '🤥', '😶', '😐', '😑', '😬', '🙄', '😯', '😦', '😧', '😮', '😲', '🥱', '😴', '🤤', '😪', '😵', '🤐', '🥴', '🤢', '🤮', '🤧', '😷', '🤒', '🤕', '🤑', '🤠', '😈', '👿', '👹', '👺', '🤡', '💩', '👻', '💀', '☠️', '👽', '👾', '🤖', '🎃', '😺', '😸', '😹', '😻', '😼', '😽', '🙀', '😿', '😾'];
                                    foreach($emojis as $emoji): 
                                    ?>
                                        <span class="emoji-item" data-emoji="<?= $emoji ?>"><?= $emoji ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <button type="button" class="btn-emoji" id="btn-emoji-mensagens" title="Adicionar emoji">
                                <i class="fa-regular fa-face-smile"></i>
                            </button>
                            <button type="button" class="btn-anexo" id="btn-anexo-mensagens" title="Anexar arquivo">
                                <i class="fa-solid fa-paperclip"></i>
                            </button>
                            <button type="submit" class="btn-enviar" title="Enviar mensagem">
                                <i class="fa-solid fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <!-- Estado vazio - nenhuma conversa selecionada -->
                    <div class="chat-vazio">
                        <i class="fa-solid fa-comments"></i>
                        <h3>Selecione uma conversa</h3>
                        <p>Escolha uma conversa da lista ao lado ou inicie uma nova conversa</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        // Define variáveis globais para o JavaScript
        window.usuarioLogadoId = <?= $idusuario_logado ?>;
        window.usuarioLogadoFoto = '<?= htmlspecialchars($_SESSION['foto_perfil']) ?>';
        
        // Se há conversa selecionada no mobile, força a abertura do chat
        <?php if ($conversa_selecionada && $outro_usuario_chat && isset($_GET['conversa'])): ?>
        // Script simplificado que garante abertura imediata
        (function() {
            function abrirChat() {
                if (window.innerWidth <= 768) {
                    var chat = document.querySelector('.chat-panel.mobile-chat-active');
                    var conversas = document.querySelector('.conversas-panel.mobile-conversas-hidden');
                    
                    if (chat) {
                        chat.style.cssText = 'transform: translateX(0) !important; display: flex !important; z-index: 1001 !important; position: absolute !important; right: 0 !important; top: 0 !important; width: 100% !important; height: 100vh !important;';
                        chat.classList.add('active');
                    }
                    
                    if (conversas) {
                        conversas.style.cssText = 'transform: translateX(-100%) !important; display: none !important;';
                        conversas.classList.add('hidden');
                    }
                }
            }
            
            // Executa imediatamente
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', abrirChat);
            } else {
                abrirChat();
            }
            
            // Fallbacks
            setTimeout(abrirChat, 50);
            setTimeout(abrirChat, 150);
        })();
        <?php endif; ?>
    </script>
    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/tema.js"></script>
    <script src="../assets/js/mobile-menu.js"></script>
    <script src="../assets/js/mensagens.js"></script>
</body>
</html>

