<?php
include("login/incs/valida-sessao.php");
require_once "login/src/ConexaoBD.php";
require_once "login/src/PostagemDAO.php";

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
    $sqlExtras = "SELECT genero, esportes_favoritos, objetivos FROM usuarios WHERE idusuarios = ?";
    $stmtExtras = $conexao->prepare($sqlExtras);
    $stmtExtras->bindParam(1, $idusuario);
    $stmtExtras->execute();
    $extras = $stmtExtras->fetch(PDO::FETCH_ASSOC);
    if ($extras) {
        $usuario['genero'] = $extras['genero'] ?? null;
        $usuario['esportes_favoritos'] = $extras['esportes_favoritos'] ?? null;
        $usuario['objetivos'] = $extras['objetivos'] ?? null;
    }
} catch (PDOException $e) {
    // Colunas não existem ainda, continua sem elas
    $usuario['genero'] = null;
    $usuario['esportes_favoritos'] = null;
    $usuario['objetivos'] = null;
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
require_once "login/src/UsuarioDAO.php";
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
    <link rel="stylesheet" href="css/feed.css">
    <link rel="stylesheet" href="css/perfil.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  
    <div class="container">
        <!-- Sidebar esquerda -->
        <aside class="sidebar">
            <div class="logo">
                <img src="/img/logo1.png" alt="Logo SportForYou">
            </div>
            <?php $paginaAtual = basename($_SERVER['PHP_SELF']); ?>
            <nav>
                <ul>
                    <li class="<?= $paginaAtual == 'home.php' ? 'ativo' : '' ?>"><a href="home.php"><i class="fa-solid fa-house"></i> Feed</a></li>
                    <li class="<?= $paginaAtual == 'esportes.php' ? 'ativo' : '' ?>"><a href="esportes.php"><i class="fa-solid fa-gamepad"></i> Esportes</a></li>
                    <li class="<?= $paginaAtual == 'eventos.php' ? 'ativo' : '' ?>"><a href="eventos.php"><i class="fa-solid fa-calendar-days"></i> Eventos</a></li>
                    <li class="<?= $paginaAtual == 'configuracoes.php' ? 'ativo' : '' ?>"><a href="configuracoes.php"><i class="fa-solid fa-gear"></i> Configurações</a></li>
                </ul>
            </nav>
            
            <div class="usuario">
                <div class="usuario-topo"></div>
                <div class="usuario-conteudo">
                    <a href="perfil.php?id=<?= $_SESSION['idusuarios'] ?>" class="perfil-link-usuario" style="display: flex; align-items: center; text-decoration: none; color: inherit; flex: 1;">
                        <img src="/login/uploads/<?= htmlspecialchars($_SESSION['foto_perfil']) ?>" alt="Foto de perfil">
                        <div class="user-info">
                            <span class="nome"><?= htmlspecialchars($_SESSION['nome']) ?></span>
                            <span class="nome_usuario">@<?= htmlspecialchars($_SESSION['nome_usuario']) ?></span>
                        </div>
                    </a>
                    <a href="/login/logout.php" class="logout" title="Sair" style="margin-left: auto;">
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
                        <img src="login/uploads/<?= htmlspecialchars($usuario['foto_perfil']) ?>" alt="Foto de perfil" class="perfil-foto">
                    </div>
                    <div class="perfil-info-header">
                        <h1 class="perfil-nome"><?= htmlspecialchars($usuario['nome']) ?></h1>
                        <p class="perfil-handle">@<?= htmlspecialchars($usuario['nome_usuario']) ?></p>
                        <p class="perfil-detalhes">
                            <?php if ($dataNascimento): ?>
                                Data de Nascimento: <?= $dataNascimento ?>
                            <?php endif; ?>
                            <?php if ($dataNascimento): ?> | <?php endif; ?>
                            Gênero: <?php echo isset($usuario['genero']) && $usuario['genero'] ? htmlspecialchars($usuario['genero']) : 'Não informado'; ?>
                        </p>
                        <?php if ($idusuario_logado == $idusuario): ?>
                            <button class="btn-editar-perfil" onclick="abrirModalEditar()">Editar Perfil</button>
                        <?php else: ?>
                            <?php if ($estaSeguindo): ?>
                                <a href="deixar_seguir.php?idseguidor=<?= $idusuario ?>" class="btn-seguir">Deixar de seguir</a>
                            <?php else: ?>
                                <a href="seguir.php?idseguidor=<?= $idusuario ?>" class="btn-seguir">Seguir</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <div class="perfil-stats">
                        <span class="stat-item">Seguidores: <?= $totalSeguidores ?></span>
                        <span class="stat-item">Seguindo: <?= $totalSeguindo ?></span>
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
                    <?php if (empty($esportesLista)): ?>
                        <p class="sem-dados">Nenhum esporte favorito cadastrado.</p>
                    <?php else: ?>
                        <ul class="esportes-list">
                            <?php foreach ($esportesLista as $esporte): ?>
                                <li><?= htmlspecialchars($esporte) ?></li>
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
                                        <img src="login/uploads/<?= htmlspecialchars($post['foto']) ?>" alt="Postagem" class="postagem-imagem">
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
                                <img src="login/uploads/<?= htmlspecialchars($user['foto_perfil']) ?>" alt="<?= htmlspecialchars($user['nome_usuario']) ?>" width="40" height="40">
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
                                <a href="deixar_seguir.php?idseguidor=<?= $user['idusuarios'] ?>" class="seguir-btn">Deixar de seguir</a>
                            <?php else: ?>
                                <a href="seguir.php?idseguidor=<?= $user['idusuarios'] ?>" class="seguir-btn">Seguir</a>
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
            <form id="form-editar-perfil" method="POST" action="atualizar_perfil.php" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Foto de Perfil</label>
                        <div class="upload-container">
                            <img id="preview-foto-perfil" src="login/uploads/<?= htmlspecialchars($usuario['foto_perfil']) ?>" alt="Foto de perfil" class="upload-preview">
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
                        <label>Esportes Favoritos</label>
                        <div class="esportes-grid">
                            <?php 
                            $esportes = ['Futebol', 'Basquete', 'Vôlei', 'Tênis', 'Natação', 'Corrida', 'Ciclismo', 'Sinuca', 'Bocha', 'Futebol Americano', 'Rugby', 'Handball', 'Surf', 'Skate', 'Judô', 'Jiu-jitsu', 'Boxe', 'MMA'];
                            $esportesUsuario = isset($usuario['esportes_favoritos']) && $usuario['esportes_favoritos'] ? json_decode($usuario['esportes_favoritos'], true) : [];
                            foreach ($esportes as $esporte): 
                                $checked = is_array($esportesUsuario) && in_array($esporte, $esportesUsuario) ? 'checked' : '';
                            ?>
                                <label class="checkbox-esporte">
                                    <input type="checkbox" name="esportes_favoritos[]" value="<?= htmlspecialchars($esporte) ?>" <?= $checked ?>>
                                    <span><?= htmlspecialchars($esporte) ?></span>
                                </label>
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

        function previewFoto(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview-foto-perfil').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Fechar modal ao clicar fora
        document.getElementById('modal-editar-perfil').addEventListener('click', function(e) {
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
    </script>
</body>
</html>
