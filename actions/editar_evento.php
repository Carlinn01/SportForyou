<?php
include("../login/incs/valida-sessao.php");
require_once "../login/src/ConexaoBD.php";
require_once "../login/src/EventoDAO.php";

$idusuario_logado = $_SESSION['idusuarios'];
$idevento = $_GET['id'] ?? null;

if (!$idevento || !is_numeric($idevento)) {
    header("Location: ../pages/eventos.php?erro=" . urlencode("ID de evento inválido"));
    exit;
}

$idevento = (int)$idevento;
$evento = EventoDAO::buscarPorId($idevento);

if (!$evento) {
    header("Location: ../pages/eventos.php?erro=" . urlencode("Evento não encontrado"));
    exit;
}

if ($evento['organizador_id'] != $idusuario_logado) {
    header("Location: ../pages/eventos.php?erro=" . urlencode("Você não tem permissão para editar este evento"));
    exit;
}

$mensagem = $_GET['mensagem'] ?? '';
$erro = $_GET['erro'] ?? '';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Evento - SportForYou</title>
    <link rel="stylesheet" href="../assets/css/feed.css">
    <link rel="stylesheet" href="../assets/css/eventos.css">
    <link rel="stylesheet" href="../assets/css/criar_evento.css">
    <link rel="stylesheet" href="../assets/css/tema-escuro.css">
    <link rel="stylesheet" href="../assets/css/responsivo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="../assets/js/tema.js"></script>
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
                    <li class="<?= $paginaAtual == 'home.php' ? 'ativo' : '' ?>"><a href="../pages/home.php"><i class="fa-solid fa-house"></i> Feed</a></li>
                    <li class="<?= $paginaAtual == 'mensagens.php' ? 'ativo' : '' ?>"><a href="../pages/mensagens.php"><i class="fa-solid fa-message"></i> Mensagens</a></li>
                    <li class="<?= $paginaAtual == 'eventos.php' ? 'ativo' : '' ?>"><a href="../pages/eventos.php"><i class="fa-solid fa-calendar-days"></i> Eventos</a></li>
                    <li class="<?= $paginaAtual == 'configuracoes.php' ? 'ativo' : '' ?>"><a href="../pages/configuracoes.php"><i class="fa-solid fa-gear"></i> Configurações</a></li>
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
            <header class="eventos-header">
                <h1>Editar Evento</h1>
                <a href="../pages/eventos.php" class="btn-voltar">
                    <i class="fa-solid fa-arrow-left"></i> Voltar
                </a>
            </header>

            <?php if ($mensagem): ?>
                <div class="alert alert-success"><?= htmlspecialchars($mensagem) ?></div>
            <?php endif; ?>

            <?php if ($erro): ?>
                <div class="alert alert-error"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <div class="form-container">
                <form method="POST" action="../actions/processar_evento.php" enctype="multipart/form-data" class="evento-form">
                    <input type="hidden" name="idevento" value="<?= $evento['idevento'] ?>">
                    
                    <div class="form-group">
                        <label for="titulo">Título do Evento *</label>
                        <input type="text" id="titulo" name="titulo" required maxlength="255" value="<?= htmlspecialchars($evento['titulo']) ?>">
                    </div>

                    <div class="form-group">
                        <label for="descricao">Descrição</label>
                        <textarea id="descricao" name="descricao" rows="4"><?= htmlspecialchars($evento['descricao'] ?? '') ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="tipo_esporte">Tipo de Esporte *</label>
                            <select id="tipo_esporte" name="tipo_esporte" required>
                                <option value="">Selecione...</option>
                                <?php 
                                $esportes = ['Futebol', 'Basquete', 'Vôlei', 'Tênis', 'Natação', 'Corrida', 'Ciclismo', 'Sinuca', 'Bocha', 'Futebol Americano', 'Rugby', 'Handball', 'Surf', 'Skate', 'Judô', 'Jiu-jitsu', 'Boxe', 'MMA'];
                                foreach ($esportes as $esporte): 
                                    $selected = $evento['tipo_esporte'] == $esporte ? 'selected' : '';
                                ?>
                                    <option value="<?= htmlspecialchars($esporte) ?>" <?= $selected ?>><?= htmlspecialchars($esporte) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="data_evento">Data do Evento *</label>
                            <input type="date" id="data_evento" name="data_evento" required value="<?= $evento['data_evento'] ?>">
                        </div>

                        <div class="form-group">
                            <label for="hora_evento">Hora do Evento</label>
                            <input type="time" id="hora_evento" name="hora_evento" value="<?= $evento['hora_evento'] ?? '' ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="local">Local *</label>
                            <input type="text" id="local" name="local" required maxlength="255" value="<?= htmlspecialchars($evento['local']) ?>">
                        </div>

                        <div class="form-group">
                            <label for="cidade">Cidade</label>
                            <input type="text" id="cidade" name="cidade" maxlength="100" value="<?= htmlspecialchars($evento['cidade'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label for="estado">Estado</label>
                            <select id="estado" name="estado">
                                <option value="">Selecione...</option>
                                <?php 
                                $estados = ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'];
                                foreach ($estados as $estado): 
                                    $selected = ($evento['estado'] ?? '') == $estado ? 'selected' : '';
                                ?>
                                    <option value="<?= $estado ?>" <?= $selected ?>><?= $estado ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="foto">Foto do Evento</label>
                        <?php if ($evento['foto']): ?>
                            <div class="foto-atual">
                                <img src="../login/uploads/<?= htmlspecialchars($evento['foto']) ?>" alt="Foto atual" style="max-width: 200px; border-radius: 8px; margin-bottom: 10px;">
                                <p style="font-size: 12px; color: #666;">Foto atual. Selecione uma nova para substituir.</p>
                            </div>
                        <?php endif; ?>
                        <input type="file" id="foto" name="foto" accept="image/*">
                        <small>Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 5MB</small>
                    </div>

                    <div class="form-actions">
                        <a href="../pages/eventos.php" class="btn-cancelar">Cancelar</a>
                        <button type="submit" class="btn-salvar">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
    <script src="../assets/js/tema.js"></script>
    <script src="../assets/js/mobile-menu.js"></script>
</body>
</html>

