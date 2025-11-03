<?php
include("../login/incs/valida-sessao.php");

$mensagem = $_GET['mensagem'] ?? '';
$erro = $_GET['erro'] ?? '';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Evento - SportForYou</title>
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
                <h1>Cadastrar Evento</h1>
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
                    <div class="form-group">
                        <label for="titulo">Título do Evento *</label>
                        <input type="text" id="titulo" name="titulo" required maxlength="255" placeholder="Ex: Maratona da Cidade">
                    </div>

                    <div class="form-group">
                        <label for="descricao">Descrição</label>
                        <textarea id="descricao" name="descricao" rows="4" placeholder="Descreva o evento..."></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="tipo_esporte">Tipo de Esporte *</label>
                            <select id="tipo_esporte" name="tipo_esporte" required>
                                <option value="">Selecione...</option>
                                <option value="Futebol">Futebol</option>
                                <option value="Basquete">Basquete</option>
                                <option value="Vôlei">Vôlei</option>
                                <option value="Tênis">Tênis</option>
                                <option value="Natação">Natação</option>
                                <option value="Corrida">Corrida</option>
                                <option value="Ciclismo">Ciclismo</option>
                                <option value="Sinuca">Sinuca</option>
                                <option value="Bocha">Bocha</option>
                                <option value="Futebol Americano">Futebol Americano</option>
                                <option value="Rugby">Rugby</option>
                                <option value="Handball">Handball</option>
                                <option value="Surf">Surf</option>
                                <option value="Skate">Skate</option>
                                <option value="Judô">Judô</option>
                                <option value="Jiu-jitsu">Jiu-jitsu</option>
                                <option value="Boxe">Boxe</option>
                                <option value="MMA">MMA</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="data_evento">Data do Evento *</label>
                            <input type="date" id="data_evento" name="data_evento" required min="<?= date('Y-m-d') ?>">
                        </div>

                        <div class="form-group">
                            <label for="hora_evento">Hora do Evento</label>
                            <input type="time" id="hora_evento" name="hora_evento">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="local">Local *</label>
                            <input type="text" id="local" name="local" required maxlength="255" placeholder="Ex: Estádio Municipal">
                        </div>

                        <div class="form-group">
                            <label for="cidade">Cidade</label>
                            <input type="text" id="cidade" name="cidade" maxlength="100" placeholder="Ex: Videira">
                        </div>

                        <div class="form-group">
                            <label for="estado">Estado</label>
                            <select id="estado" name="estado">
                                <option value="">Selecione...</option>
                                <option value="AC">AC</option>
                                <option value="AL">AL</option>
                                <option value="AP">AP</option>
                                <option value="AM">AM</option>
                                <option value="BA">BA</option>
                                <option value="CE">CE</option>
                                <option value="DF">DF</option>
                                <option value="ES">ES</option>
                                <option value="GO">GO</option>
                                <option value="MA">MA</option>
                                <option value="MT">MT</option>
                                <option value="MS">MS</option>
                                <option value="MG">MG</option>
                                <option value="PA">PA</option>
                                <option value="PB">PB</option>
                                <option value="PR">PR</option>
                                <option value="PE">PE</option>
                                <option value="PI">PI</option>
                                <option value="RJ">RJ</option>
                                <option value="RN">RN</option>
                                <option value="RS">RS</option>
                                <option value="RO">RO</option>
                                <option value="RR">RR</option>
                                <option value="SC">SC</option>
                                <option value="SP">SP</option>
                                <option value="SE">SE</option>
                                <option value="TO">TO</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="foto">Foto do Evento</label>
                        <input type="file" id="foto" name="foto" accept="image/*">
                        <small>Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 5MB</small>
                    </div>

                    <div class="form-actions">
                        <a href="../pages/eventos.php" class="btn-cancelar">Cancelar</a>
                        <button type="submit" class="btn-salvar">Cadastrar Evento</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
    <script src="../assets/js/mobile-menu.js"></script>
</body>
</html>

