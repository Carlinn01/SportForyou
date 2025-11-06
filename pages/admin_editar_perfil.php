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

$idusuario_editar = $_GET['id'] ?? null;

if (!$idusuario_editar) {
    $_SESSION['msg'] = 'ID do usuário não informado.';
    $_SESSION['msg_tipo'] = 'erro';
    header("Location: admin_usuarios.php");
    exit;
}

// Busca dados do usuário
$usuario = null;
try {
    $sql = "SELECT * FROM usuarios WHERE idusuarios = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$idusuario_editar]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erro ao buscar usuário: " . $e->getMessage());
}

if (!$usuario) {
    $_SESSION['msg'] = 'Usuário não encontrado.';
    $_SESSION['msg_tipo'] = 'erro';
    header("Location: admin_usuarios.php");
    exit;
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
    <title>Editar Perfil - Admin</title>
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
                    <i class="fa-solid fa-user-edit"></i>
                    <h1>Editar Perfil - <?= htmlspecialchars($usuario['nome']) ?></h1>
                </div>
                <a href="admin_usuarios.php" class="btn-admin btn-admin-view" style="margin-top: 15px;">
                    <i class="fa-solid fa-arrow-left"></i>
                    Voltar
                </a>
            </div>

            <!-- Formulário de Edição -->
            <div class="config-card">
                <h2 class="card-title">Dados do Usuário</h2>
                <form method="POST" action="../actions/admin_atualizar_perfil.php" enctype="multipart/form-data" class="reportar-form">
                    <input type="hidden" name="idusuario" value="<?= $usuario['idusuarios'] ?>">
                    
                    <div class="form-group">
                        <label for="nome">Nome *</label>
                        <input type="text" id="nome" name="nome" required value="<?= htmlspecialchars($usuario['nome']) ?>" maxlength="45">
                    </div>
                    
                    <div class="form-group">
                        <label for="nome_usuario">Nome de Usuário *</label>
                        <input type="text" id="nome_usuario" name="nome_usuario" required value="<?= htmlspecialchars($usuario['nome_usuario']) ?>" maxlength="45">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required value="<?= htmlspecialchars($usuario['email']) ?>" maxlength="45">
                    </div>
                    
                    <div class="form-group">
                        <label for="nascimento">Data de Nascimento</label>
                        <input type="date" id="nascimento" name="nascimento" value="<?= $usuario['nascimento'] ?? '' ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="genero">Gênero</label>
                        <select id="genero" name="genero">
                            <option value="">Selecione</option>
                            <option value="Masculino" <?= ($usuario['genero'] ?? '') == 'Masculino' ? 'selected' : '' ?>>Masculino</option>
                            <option value="Feminino" <?= ($usuario['genero'] ?? '') == 'Feminino' ? 'selected' : '' ?>>Feminino</option>
                            <option value="Outro" <?= ($usuario['genero'] ?? '') == 'Outro' ? 'selected' : '' ?>>Outro</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="objetivos">Objetivos</label>
                        <textarea id="objetivos" name="objetivos" rows="4" placeholder="Objetivos pessoais..."><?= htmlspecialchars($usuario['objetivos'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="descricao_pessoal">Descrição Pessoal</label>
                        <textarea id="descricao_pessoal" name="descricao_pessoal" rows="4" placeholder="Descrição pessoal..."><?= htmlspecialchars($usuario['descricao_pessoal'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="tipo_treino_favorito">Tipo de Treino Favorito</label>
                        <input type="text" id="tipo_treino_favorito" name="tipo_treino_favorito" value="<?= htmlspecialchars($usuario['tipo_treino_favorito'] ?? '') ?>" maxlength="100">
                    </div>
                    
                    <div class="form-group">
                        <label for="is_admin">É Administrador?</label>
                        <select id="is_admin" name="is_admin">
                            <option value="0" <?= ($usuario['is_admin'] ?? 0) == 0 ? 'selected' : '' ?>>Não</option>
                            <option value="1" <?= ($usuario['is_admin'] ?? 0) == 1 ? 'selected' : '' ?>>Sim</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="foto_perfil">Foto de Perfil</label>
                        <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*">
                        <?php if ($usuario['foto_perfil']): ?>
                            <div style="margin-top: 10px;">
                                <img src="../login/uploads/<?= htmlspecialchars($usuario['foto_perfil']) ?>" alt="Foto atual" style="width: 100px; height: 100px; border-radius: 8px; object-fit: cover;">
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-actions">
                        <a href="admin_usuarios.php" class="btn-cancelar">
                            <i class="fa-solid fa-times"></i>
                            Cancelar
                        </a>
                        <button type="submit" class="btn-salvar">
                            <i class="fa-solid fa-floppy-disk"></i>
                            Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script src="../assets/js/tema.js"></script>
    <script src="../assets/js/mobile-menu.js"></script>
</body>
</html>

