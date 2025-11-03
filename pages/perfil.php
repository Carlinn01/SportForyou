<?php
include("../login/incs/valida-sessao.php");
require_once "../login/src/ConexaoBD.php";
require_once "../login/src/PostagemDAO.php";

if (!isset($_GET['id'])) {
    header("Location: home.php");
    exit;
}

$idusuario = $_GET['id'];
$idusuario_logado = $_SESSION['idusuarios'];

$conexao = ConexaoBD::conectar();

// Pega dados do usuário (tenta buscar gênero e esportes_favoritos também)
$sql = "SELECT idusuarios, nome, nome_usuario, email, nascimento, foto_perfil 
        FROM usuarios 
        WHERE idusuarios = ?";
$stmt = $conexao->prepare($sql);
$stmt->bindParam(1, $idusuario);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Tenta buscar colunas adicionais se existirem
try {
    $sqlExtras = "SELECT genero, esportes_favoritos, objetivos, descricao_pessoal, tipo_treino_favorito, esportes_detalhados FROM usuarios WHERE idusuarios = ?";
    $stmtExtras = $conexao->prepare($sqlExtras);
    $stmtExtras->bindParam(1, $idusuario);
    $stmtExtras->execute();
    $extras = $stmtExtras->fetch(PDO::FETCH_ASSOC);
    if ($extras) {
        $usuario['genero'] = $extras['genero'] ?? null;
        $usuario['esportes_favoritos'] = $extras['esportes_favoritos'] ?? null;
        $usuario['objetivos'] = $extras['objetivos'] ?? null;
        $usuario['descricao_pessoal'] = $extras['descricao_pessoal'] ?? null;
        $usuario['tipo_treino_favorito'] = $extras['tipo_treino_favorito'] ?? null;
        $usuario['esportes_detalhados'] = $extras['esportes_detalhados'] ?? null;
    }
} catch (PDOException $e) {
    // Colunas não existem ainda, continua sem elas
    $usuario['genero'] = null;
    $usuario['esportes_favoritos'] = null;
    $usuario['objetivos'] = null;
    $usuario['descricao_pessoal'] = null;
    $usuario['tipo_treino_favorito'] = null;
    $usuario['esportes_detalhados'] = null;
}

if (!$usuario) {
    echo "Usuário não encontrado!";
    exit;
}

// Quantos seguidores o usuário tem
$sqlSeguidores = "SELECT COUNT(*) as total FROM seguidores WHERE idusuario = ?";
$stmt = $conexao->prepare($sqlSeguidores);
$stmt->bindParam(1, $idusuario);
$stmt->execute();
$totalSeguidores = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Quantos ele segue
$sqlSeguindo = "SELECT COUNT(*) as total FROM seguidores WHERE idseguidor = ?";
$stmt = $conexao->prepare($sqlSeguindo);
$stmt->bindParam(1, $idusuario);
$stmt->execute();
$totalSeguindo = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Verifica se o usuário logado está seguindo este usuário
$sqlEstaSeguindo = "SELECT COUNT(*) as total FROM seguidores WHERE idseguidor = ? AND idusuario = ?";
$stmt = $conexao->prepare($sqlEstaSeguindo);
$stmt->execute([$idusuario_logado, $idusuario]);
$estaSeguindo = $stmt->fetch(PDO::FETCH_ASSOC)['total'] > 0;

// Busca postagens do usuário
$sqlPostagens = "SELECT p.*, 
                 (SELECT COUNT(*) FROM curtidas WHERE idpostagem = p.idpostagem) AS curtidas,
                 (SELECT COUNT(*) FROM comentarios WHERE idpostagem = p.idpostagem) AS total_comentarios
                 FROM postagens p
                 WHERE p.idusuario = ?
                 ORDER BY p.criado_em DESC
                 LIMIT 3";
$stmt = $conexao->prepare($sqlPostagens);
$stmt->bindParam(1, $idusuario);
$stmt->execute();
$postagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Busca sugestões de usuários para seguir (similar ao home)
require_once "../login/src/UsuarioDAO.php";
$sugestoes = UsuarioDAO::listarSugestoes($idusuario_logado, 5);

// Formata data de nascimento
$dataNascimento = $usuario['nascimento'] ? date('d/m/Y', strtotime($usuario['nascimento'])) : '';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?= htmlspecialchars($usuario['nome_usuario']) ?> - SportForYou</title>
    <link rel="stylesheet" href="../assets/css/feed.css">
    <link rel="stylesheet" href="../assets/css/perfil.css">
    <link rel="stylesheet" href="../assets/css/tema-escuro.css">
    <link rel="stylesheet" href="../assets/css/responsivo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="../assets/js/tema.js"></script>
    <script src="../assets/js/mobile-menu.js"></script>
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
            <div class="perfil-content">
                <!-- Cabeçalho do Perfil -->
                <div class="perfil-header">
                    <div class="perfil-foto-container">
                        <img src="../login/uploads/<?= htmlspecialchars($usuario['foto_perfil']) ?>" alt="Foto de perfil" class="perfil-foto">
                    </div>
                    <div class="perfil-info-header">
                        <h1 class="perfil-nome"><?= htmlspecialchars($usuario['nome']) ?></h1>
                        <p class="perfil-handle">@<?= htmlspecialchars($usuario['nome_usuario']) ?></p>
                        <div class="perfil-detalhes">
                            <?php if ($dataNascimento): ?>
                                <p class="perfil-detalhe-item">Data de Nascimento: <?= $dataNascimento ?></p>
                            <?php endif; ?>
                            <p class="perfil-detalhe-item">Gênero: <?php echo isset($usuario['genero']) && $usuario['genero'] ? htmlspecialchars($usuario['genero']) : 'Não informado'; ?></p>
                            <?php if (isset($usuario['tipo_treino_favorito']) && $usuario['tipo_treino_favorito']): ?>
                                <p class="perfil-detalhe-item">Tipo de Treino Favorito: <?= htmlspecialchars($usuario['tipo_treino_favorito']) ?></p>
                            <?php endif; ?>
                        </div>
                        <?php if ($idusuario_logado == $idusuario): ?>
                            <button class="btn-editar-perfil" onclick="abrirModalEditar()">Editar Perfil</button>
                        <?php else: ?>
                            <?php if ($estaSeguindo): ?>
                                <a href="../actions/deixar_seguir.php?idseguidor=<?= $idusuario ?>" class="btn-seguir">Deixar de seguir</a>
                            <?php else: ?>
                                <a href="../actions/seguir.php?idseguidor=<?= $idusuario ?>" class="btn-seguir">Seguir</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <div class="perfil-stats">
                        <button class="stat-item clickable" onclick="abrirModalSeguidores(<?= $idusuario ?>)">
                            <strong><?= $totalSeguidores ?></strong>
                            <span>Seguidores</span>
                        </button>
                        <button class="stat-item clickable" onclick="abrirModalSeguindo(<?= $idusuario ?>)">
                            <strong><?= $totalSeguindo ?></strong>
                            <span>Seguindo</span>
                        </button>
                    </div>
                </div>

                <!-- Seção Esportes Favoritos -->
                <div class="perfil-section">
                    <h2 class="section-title">Esportes Favoritos:</h2>
                    <?php 
                    $esportesLista = isset($usuario['esportes_favoritos']) && $usuario['esportes_favoritos'] 
                        ? json_decode($usuario['esportes_favoritos'], true) 
                        : [];
                    ?>
                    <?php 
                    $esportesDetalhados = isset($usuario['esportes_detalhados']) && $usuario['esportes_detalhados'] 
                        ? json_decode($usuario['esportes_detalhados'], true) 
                        : [];
                    
                    // Criar um array associativo para facilitar busca
                    $esportesInfo = [];
                    if (is_array($esportesDetalhados)) {
                        foreach ($esportesDetalhados as $det) {
                            if (isset($det['esporte'])) {
                                $esportesInfo[$det['esporte']] = $det;
                            }
                        }
                    }
                    ?>
                    <?php if (empty($esportesLista)): ?>
                        <p class="sem-dados">Nenhum esporte favorito cadastrado.</p>
                    <?php else: ?>
                        <ul class="esportes-list">
                            <?php foreach ($esportesLista as $esporte): 
                                $info = isset($esportesInfo[$esporte]) ? $esportesInfo[$esporte] : null;
                                $nivel = $info && isset($info['nivel']) && $info['nivel'] ? $info['nivel'] : null;
                                $frequencia = $info && isset($info['frequencia']) && $info['frequencia'] ? $info['frequencia'] : null;
                            ?>
                                <li class="esporte-item">
                                    <span class="esporte-nome"><?= htmlspecialchars($esporte) ?></span>
                                    <?php if ($nivel || $frequencia): ?>
                                        <span class="esporte-info">
                                            <?php if ($nivel): ?>
                                                <span class="esporte-badge esporte-nivel-badge"><?= htmlspecialchars($nivel) ?></span>
                                            <?php endif; ?>
                                            <?php if ($frequencia): ?>
                                                <span class="esporte-badge esporte-frequencia-badge"><?= htmlspecialchars($frequencia) ?></span>
                                            <?php endif; ?>
                                        </span>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <!-- Seção Objetivos Pessoais -->
                <div class="perfil-section">
                    <h2 class="section-title">Objetivos Pessoais:</h2>
                    <?php if (empty($usuario['objetivos'])): ?>
                        <p class="sem-dados">Nenhum objetivo pessoal cadastrado.</p>
                    <?php else: ?>
                        <p class="objetivo-text"><?= htmlspecialchars($usuario['objetivos']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Grade de Postagens -->
                <div class="perfil-section">
                    <h2 class="section-title">Postagens</h2>
                    <div class="postagens-grid">
                        <?php if (empty($postagens)): ?>
                            <p class="sem-postagens">Este usuário ainda não fez nenhuma postagem.</p>
                        <?php else: ?>
                            <?php foreach ($postagens as $post): ?>
                                <a href="postagem.php?id=<?= $post['idpostagem'] ?>" class="postagem-card">
                                    <?php if ($post['foto']): ?>
                                        <img src="../login/uploads/<?= htmlspecialchars($post['foto']) ?>" alt="Postagem" class="postagem-imagem">
                                    <?php else: ?>
                                        <div class="postagem-sem-imagem">
                                            <p><?= htmlspecialchars(function_exists('mb_substr') ? mb_substr($post['texto'] ?? '', 0, 100) : substr($post['texto'] ?? '', 0, 100)) ?></p>
                                        </div>
                                    <?php endif; ?>
                                    <div class="postagem-actions">
                                        <span class="postagem-icon"><i class="fa-regular fa-comment"></i></span>
                                        <span class="postagem-icon"><i class="fa-solid fa-share"></i></span>
                                        <span class="postagem-icon"><i class="fa-regular fa-heart"></i> <?= $post['curtidas'] ?? 0 ?></span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>

        <!-- Lateral direita -->
        <aside class="rightbar">
            <h3>Sugestões</h3>
            <ul>
                <?php if (empty($sugestoes)): ?>
                    <li class="sem-sugestoes">Nenhuma sugestão no momento.</li>
                <?php else: ?>
                    <?php foreach($sugestoes as $user): 
                        $primeiroNome = explode(' ', $user['nome'])[0];
                    ?>
                        <li class="sugestao-item">
                            <a href="perfil.php?id=<?= $user['idusuarios'] ?>" class="perfil-link">
                                <img src="../login/uploads/<?= htmlspecialchars($user['foto_perfil']) ?>" alt="<?= htmlspecialchars($user['nome_usuario']) ?>" width="40" height="40">
                                <div class="user-info">
                                    <span class="nome"><?= htmlspecialchars($primeiroNome) ?></span>
                                    <span class="nome_usuario">@<?= htmlspecialchars($user['nome_usuario']) ?></span>
                                </div>
                            </a>
                            <?php
                            // Verifica se o usuário logado já está seguindo
                            $sqlVerifica = "SELECT COUNT(*) FROM seguidores WHERE idseguidor = ? AND idusuario = ?";
                            $stmtVerifica = $conexao->prepare($sqlVerifica);
                            $stmtVerifica->execute([$idusuario_logado, $user['idusuarios']]);
                            $jaSeguindo = $stmtVerifica->fetchColumn() > 0;
                            ?>
                            <?php if ($jaSeguindo): ?>
                                <a href="../actions/deixar_seguir.php?idseguidor=<?= $user['idusuarios'] ?>" class="seguir-btn">Deixar de seguir</a>
                            <?php else: ?>
                                <a href="../actions/seguir.php?idseguidor=<?= $user['idusuarios'] ?>" class="seguir-btn">Seguir</a>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </aside>
    </div>

    <!-- Modal de Editar Perfil -->
    <div id="modal-editar-perfil" class="modal hidden">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Editar Perfil</h2>
                <button class="modal-close" onclick="fecharModalEditar()">&times;</button>
            </div>
            <form id="form-editar-perfil" method="POST" action="../actions/atualizar_perfil.php" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Foto de Perfil</label>
                        <div class="upload-container">
                            <img id="preview-foto-perfil" src="../login/uploads/<?= htmlspecialchars($usuario['foto_perfil']) ?>" alt="Foto de perfil" class="upload-preview">
                            <input type="file" id="foto-perfil" name="foto_perfil" accept="image/*" onchange="previewFoto(this)">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="nome">Nome</label>
                        <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="nome_usuario">Nome de Usuário</label>
                        <input type="text" id="nome_usuario" name="nome_usuario" value="<?= htmlspecialchars($usuario['nome_usuario']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="nascimento">Data de Nascimento</label>
                        <input type="date" id="nascimento" name="nascimento" value="<?= $usuario['nascimento'] ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="genero">Gênero</label>
                        <select id="genero" name="genero">
                            <option value="">Selecione</option>
                            <option value="Masculino" <?= (isset($usuario['genero']) && $usuario['genero'] == 'Masculino') ? 'selected' : '' ?>>Masculino</option>
                            <option value="Feminino" <?= (isset($usuario['genero']) && $usuario['genero'] == 'Feminino') ? 'selected' : '' ?>>Feminino</option>
                            <option value="Outro" <?= (isset($usuario['genero']) && $usuario['genero'] == 'Outro') ? 'selected' : '' ?>>Outro</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="descricao_pessoal">Descrição Pessoal</label>
                        <textarea id="descricao_pessoal" name="descricao_pessoal" rows="3" placeholder="Conte um pouco sobre você..."><?= htmlspecialchars(isset($usuario['descricao_pessoal']) ? $usuario['descricao_pessoal'] : '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="tipo_treino_favorito">Tipo de Treino Favorito</label>
                        <select id="tipo_treino_favorito" name="tipo_treino_favorito">
                            <option value="">Selecione</option>
                            <option value="Cardio" <?= (isset($usuario['tipo_treino_favorito']) && $usuario['tipo_treino_favorito'] == 'Cardio') ? 'selected' : '' ?>>Cardio</option>
                            <option value="Força" <?= (isset($usuario['tipo_treino_favorito']) && $usuario['tipo_treino_favorito'] == 'Força') ? 'selected' : '' ?>>Força</option>
                            <option value="Resistência" <?= (isset($usuario['tipo_treino_favorito']) && $usuario['tipo_treino_favorito'] == 'Resistência') ? 'selected' : '' ?>>Resistência</option>
                            <option value="Flexibilidade" <?= (isset($usuario['tipo_treino_favorito']) && $usuario['tipo_treino_favorito'] == 'Flexibilidade') ? 'selected' : '' ?>>Flexibilidade</option>
                            <option value="Funcional" <?= (isset($usuario['tipo_treino_favorito']) && $usuario['tipo_treino_favorito'] == 'Funcional') ? 'selected' : '' ?>>Funcional</option>
                            <option value="Crossfit" <?= (isset($usuario['tipo_treino_favorito']) && $usuario['tipo_treino_favorito'] == 'Crossfit') ? 'selected' : '' ?>>Crossfit</option>
                            <option value="HIIT" <?= (isset($usuario['tipo_treino_favorito']) && $usuario['tipo_treino_favorito'] == 'HIIT') ? 'selected' : '' ?>>HIIT</option>
                            <option value="Pilates" <?= (isset($usuario['tipo_treino_favorito']) && $usuario['tipo_treino_favorito'] == 'Pilates') ? 'selected' : '' ?>>Pilates</option>
                            <option value="Yoga" <?= (isset($usuario['tipo_treino_favorito']) && $usuario['tipo_treino_favorito'] == 'Yoga') ? 'selected' : '' ?>>Yoga</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Esportes Favoritos</label>
                        <div class="esportes-grid" id="esportes-grid">
                            <?php 
                            $esportes = ['Futebol', 'Basquete', 'Vôlei', 'Tênis', 'Natação', 'Corrida', 'Ciclismo', 'Sinuca', 'Bocha', 'Futebol Americano', 'Rugby', 'Handball', 'Surf', 'Skate', 'Judô', 'Jiu-jitsu', 'Boxe', 'MMA'];
                            $esportesUsuario = isset($usuario['esportes_favoritos']) && $usuario['esportes_favoritos'] ? json_decode($usuario['esportes_favoritos'], true) : [];
                            $esportesDetalhados = isset($usuario['esportes_detalhados']) && $usuario['esportes_detalhados'] ? json_decode($usuario['esportes_detalhados'], true) : [];
                            
                            foreach ($esportes as $esporte): 
                                $checked = is_array($esportesUsuario) && in_array($esporte, $esportesUsuario) ? 'checked' : '';
                                $esporteDetalhe = null;
                                if (is_array($esportesDetalhados)) {
                                    foreach ($esportesDetalhados as $det) {
                                        if (isset($det['esporte']) && $det['esporte'] == $esporte) {
                                            $esporteDetalhe = $det;
                                            break;
                                        }
                                    }
                                }
                            ?>
                                <div class="esporte-item-completo">
                                    <label class="checkbox-esporte">
                                        <input type="checkbox" name="esportes_favoritos[]" value="<?= htmlspecialchars($esporte) ?>" <?= $checked ?> data-esporte="<?= htmlspecialchars($esporte) ?>" onchange="toggleEsporteDetalhes(this)">
                                        <span><?= htmlspecialchars($esporte) ?></span>
                                    </label>
                                    <div class="esporte-detalhes" style="display: <?= $checked ? 'block' : 'none' ?>;">
                                        <select name="nivel_<?= htmlspecialchars($esporte) ?>" class="esporte-nivel" placeholder="Nível">
                                            <option value="">Nível</option>
                                            <option value="Iniciante" <?= ($esporteDetalhe && isset($esporteDetalhe['nivel']) && $esporteDetalhe['nivel'] == 'Iniciante') ? 'selected' : '' ?>>Iniciante</option>
                                            <option value="Intermediário" <?= ($esporteDetalhe && isset($esporteDetalhe['nivel']) && $esporteDetalhe['nivel'] == 'Intermediário') ? 'selected' : '' ?>>Intermediário</option>
                                            <option value="Avançado" <?= ($esporteDetalhe && isset($esporteDetalhe['nivel']) && $esporteDetalhe['nivel'] == 'Avançado') ? 'selected' : '' ?>>Avançado</option>
                                            <option value="Profissional" <?= ($esporteDetalhe && isset($esporteDetalhe['nivel']) && $esporteDetalhe['nivel'] == 'Profissional') ? 'selected' : '' ?>>Profissional</option>
                                        </select>
                                        <select name="frequencia_<?= htmlspecialchars($esporte) ?>" class="esporte-frequencia" placeholder="Frequência">
                                            <option value="">Frequência</option>
                                            <option value="Diário" <?= ($esporteDetalhe && isset($esporteDetalhe['frequencia']) && $esporteDetalhe['frequencia'] == 'Diário') ? 'selected' : '' ?>>Diário</option>
                                            <option value="5-6x/semana" <?= ($esporteDetalhe && isset($esporteDetalhe['frequencia']) && $esporteDetalhe['frequencia'] == '5-6x/semana') ? 'selected' : '' ?>>5-6x/semana</option>
                                            <option value="3-4x/semana" <?= ($esporteDetalhe && isset($esporteDetalhe['frequencia']) && $esporteDetalhe['frequencia'] == '3-4x/semana') ? 'selected' : '' ?>>3-4x/semana</option>
                                            <option value="2-3x/semana" <?= ($esporteDetalhe && isset($esporteDetalhe['frequencia']) && $esporteDetalhe['frequencia'] == '2-3x/semana') ? 'selected' : '' ?>>2-3x/semana</option>
                                            <option value="1x/semana" <?= ($esporteDetalhe && isset($esporteDetalhe['frequencia']) && $esporteDetalhe['frequencia'] == '1x/semana') ? 'selected' : '' ?>>1x/semana</option>
                                            <option value="Ocasional" <?= ($esporteDetalhe && isset($esporteDetalhe['frequencia']) && $esporteDetalhe['frequencia'] == 'Ocasional') ? 'selected' : '' ?>>Ocasional</option>
                                        </select>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="objetivos">Objetivos Pessoais</label>
                        <textarea id="objetivos" name="objetivos" rows="3" placeholder="Ex: Começar a praticar basquete"><?= htmlspecialchars(isset($usuario['objetivos']) ? $usuario['objetivos'] : '') ?></textarea>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn-cancelar" onclick="fecharModalEditar()">Cancelar</button>
                        <button type="submit" class="btn-salvar">Salvar Alterações</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function abrirModalEditar() {
            document.getElementById('modal-editar-perfil').classList.remove('hidden');
        }

        function fecharModalEditar() {
            document.getElementById('modal-editar-perfil').classList.add('hidden');
        }

        function toggleEsporteDetalhes(checkbox) {
            const esporteItem = checkbox.closest('.esporte-item-completo');
            const detalhes = esporteItem.querySelector('.esporte-detalhes');
            if (checkbox.checked) {
                detalhes.style.display = 'block';
            } else {
                detalhes.style.display = 'none';
                // Limpa os valores dos selects
                detalhes.querySelectorAll('select').forEach(select => {
                    select.value = '';
                });
            }
        }


        function previewFoto(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview-foto-perfil').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Fechar modal ao clicar fora (mas não nos inputs)
        document.getElementById('modal-editar-perfil').addEventListener('click', function(e) {
            // Só fecha se clicar diretamente no overlay (modal), não no conteúdo
            if (e.target === this) {
                fecharModalEditar();
            }
        });
        

        // Fechar modal com ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                fecharModalEditar();
            }
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

    <!-- Modal de Seguidores/Seguindo -->
    <div id="modal-seguidores" class="modal-seguidores">
        <div class="modal-content-seguidores">
            <div class="modal-header-seguidores">
                <h2 id="modal-titulo">Seguidores</h2>
                <button class="modal-close" onclick="fecharModalSeguidores()">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            <div class="modal-body-seguidores" id="modal-body-seguidores">
                <div class="loading-seguidores">
                    <i class="fa-solid fa-spinner fa-spin"></i>
                    <p>Carregando...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Modal de Seguidores/Seguindo
        function abrirModalSeguidores(usuarioId) {
            const modal = document.getElementById('modal-seguidores');
            const titulo = document.getElementById('modal-titulo');
            const body = document.getElementById('modal-body-seguidores');
            
            titulo.textContent = 'Seguidores';
            body.innerHTML = '<div class="loading-seguidores"><i class="fa-solid fa-spinner fa-spin"></i><p>Carregando...</p></div>';
            modal.style.display = 'flex';
            
            fetch(`../api/buscar_seguidores.php?usuario_id=${usuarioId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.erro) {
                        body.innerHTML = `<p class="erro-modal">${data.erro}</p>`;
                        return;
                    }
                    
                    if (data.length === 0) {
                        body.innerHTML = '<div class="sem-resultados-modal"><p>Nenhum seguidor ainda.</p></div>';
                        return;
                    }
                    
                    body.innerHTML = data.map(usuario => {
                        const primeiroNome = usuario.nome.split(' ')[0];
                        const btnTexto = usuario.eh_eu ? '' : (usuario.ja_sigo ? 'Deixar de seguir' : 'Seguir');
                        const btnClass = usuario.ja_sigo ? 'btn-deixar-seguir-modal' : 'btn-seguir-modal';
                        const btnIcon = usuario.ja_sigo ? 'fa-user-check' : 'fa-user-plus';
                        
                        const actionUrl = usuario.ja_sigo ? 'deixar_seguir' : 'seguir';
                        return '<div class="usuario-item-modal">' +
                            '<a href="perfil.php?id=' + usuario.idusuarios + '" class="usuario-info-modal">' +
                            '<img src="../login/uploads/' + usuario.foto_perfil + '" alt="' + usuario.nome_usuario + '" class="usuario-avatar-modal">' +
                            '<div class="usuario-detalhes-modal">' +
                            '<span class="usuario-nome-modal">' + primeiroNome + '</span>' +
                            '<span class="usuario-username-modal">@' + usuario.nome_usuario + '</span>' +
                            '</div>' +
                            '</a>' +
                            (!usuario.eh_eu ? '<a href="../actions/' + actionUrl + '.php?idseguidor=' + usuario.idusuarios + '" class="' + btnClass + '">' + (btnTexto ? '<i class="fa-solid ' + btnIcon + '"></i> ' + btnTexto : '') + '</a>' : '') +
                            '</div>';
                    }).join('');
                })
                .catch(error => {
                    body.innerHTML = '<div class="erro-modal"><p>Erro ao carregar seguidores.</p></div>';
                    console.error('Erro:', error);
                });
        }

        function abrirModalSeguindo(usuarioId) {
            const modal = document.getElementById('modal-seguidores');
            const titulo = document.getElementById('modal-titulo');
            const body = document.getElementById('modal-body-seguidores');
            
            titulo.textContent = 'Seguindo';
            body.innerHTML = '<div class="loading-seguidores"><i class="fa-solid fa-spinner fa-spin"></i><p>Carregando...</p></div>';
            modal.style.display = 'flex';
            
            fetch(`../api/buscar_seguindo.php?usuario_id=${usuarioId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.erro) {
                        body.innerHTML = `<p class="erro-modal">${data.erro}</p>`;
                        return;
                    }
                    
                    if (data.length === 0) {
                        body.innerHTML = '<div class="sem-resultados-modal"><p>Não está seguindo ninguém ainda.</p></div>';
                        return;
                    }
                    
                    body.innerHTML = data.map(usuario => {
                        const primeiroNome = usuario.nome.split(' ')[0];
                        const btnTexto = usuario.eh_eu ? '' : (usuario.ja_sigo ? 'Deixar de seguir' : 'Seguir');
                        const btnClass = usuario.ja_sigo ? 'btn-deixar-seguir-modal' : 'btn-seguir-modal';
                        const btnIcon = usuario.ja_sigo ? 'fa-user-check' : 'fa-user-plus';
                        
                        const actionUrl = usuario.ja_sigo ? 'deixar_seguir' : 'seguir';
                        return '<div class="usuario-item-modal">' +
                            '<a href="perfil.php?id=' + usuario.idusuarios + '" class="usuario-info-modal">' +
                            '<img src="../login/uploads/' + usuario.foto_perfil + '" alt="' + usuario.nome_usuario + '" class="usuario-avatar-modal">' +
                            '<div class="usuario-detalhes-modal">' +
                            '<span class="usuario-nome-modal">' + primeiroNome + '</span>' +
                            '<span class="usuario-username-modal">@' + usuario.nome_usuario + '</span>' +
                            '</div>' +
                            '</a>' +
                            (!usuario.eh_eu ? '<a href="../actions/' + actionUrl + '.php?idseguidor=' + usuario.idusuarios + '" class="' + btnClass + '">' + (btnTexto ? '<i class="fa-solid ' + btnIcon + '"></i> ' + btnTexto : '') + '</a>' : '') +
                            '</div>';
                    }).join('');
                })
                .catch(error => {
                    body.innerHTML = '<div class="erro-modal"><p>Erro ao carregar seguindo.</p></div>';
                    console.error('Erro:', error);
                });
        }

        function fecharModalSeguidores() {
            document.getElementById('modal-seguidores').style.display = 'none';
        }

        // Fechar modal ao clicar fora
        document.getElementById('modal-seguidores').addEventListener('click', function(e) {
            if (e.target === this) {
                fecharModalSeguidores();
            }
        });

        // Fechar modal com ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                fecharModalSeguidores();
            }
        });
    </script>
</body>
</html>
