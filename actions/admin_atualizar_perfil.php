<?php
include("../login/incs/valida-admin.php");
require_once "../login/src/ConexaoBD.php";
require_once "../login/src/CSRF.php";

$idusuario_logado = $_SESSION['idusuarios'];
$conexao = ConexaoBD::conectar();

// Validação CSRF
$token = $_POST['csrf_token'] ?? '';
if (!CSRF::validarToken($token)) {
    $_SESSION['msg'] = 'Token de segurança inválido.';
    $_SESSION['msg_tipo'] = 'erro';
    header("Location: ../pages/admin_editar_perfil.php?id=" . ($_POST['idusuario'] ?? 0));
    exit;
}


try {
    $conexao->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
} catch (PDOException $e) {
    // Ignora se falhar
}

// Validação de ID - converte para int e valida
$idusuario_editar = isset($_POST['idusuario']) ? (int)$_POST['idusuario'] : 0;

if ($idusuario_editar <= 0) {
    $_SESSION['msg'] = 'ID do usuário inválido.';
    $_SESSION['msg_tipo'] = 'erro';
    header("Location: ../pages/admin_usuarios.php");
    exit;
}

// Função para verificar se uma coluna existe
function colunaExiste($pdo, $tabela, $coluna) {
    try {
        $sql = "SHOW COLUMNS FROM $tabela LIKE '$coluna'";
        $stmt = $pdo->query($sql);
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        return false;
    }
}

// Processa dados do formulário
$nome = trim($_POST['nome'] ?? '');
$nome_usuario = trim($_POST['nome_usuario'] ?? '');
$email = trim($_POST['email'] ?? '');
$nascimento = $_POST['nascimento'] ?? null;
$genero = $_POST['genero'] ?? '';
$objetivos = trim($_POST['objetivos'] ?? '');
$descricao_pessoal = trim($_POST['descricao_pessoal'] ?? '');
$tipo_treino_favorito = trim($_POST['tipo_treino_favorito'] ?? '');
$is_admin = isset($_POST['is_admin']) ? (int)$_POST['is_admin'] : 0;

// Validações
if (empty($nome) || empty($nome_usuario) || empty($email)) {
    $_SESSION['msg'] = 'Nome, nome de usuário e email são obrigatórios.';
    $_SESSION['msg_tipo'] = 'erro';
    header("Location: ../pages/admin_editar_perfil.php?id=" . $idusuario_editar);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['msg'] = 'Email inválido.';
    $_SESSION['msg_tipo'] = 'erro';
    header("Location: ../pages/admin_editar_perfil.php?id=" . $idusuario_editar);
    exit;
}

// Verifica se o nome de usuário já existe (exceto para o próprio usuário)
$sqlVerificar = "SELECT idusuarios FROM usuarios WHERE nome_usuario = ? AND idusuarios != ?";
$stmtVerificar = $conexao->prepare($sqlVerificar);
$stmtVerificar->execute([$nome_usuario, $idusuario_editar]);

if ($stmtVerificar->rowCount() > 0) {
    $_SESSION['msg'] = 'Este nome de usuário já está em uso.';
    $_SESSION['msg_tipo'] = 'erro';
    header("Location: ../pages/admin_editar_perfil.php?id=" . $idusuario_editar);
    exit;
}

//  upload de foto
$foto_perfil = null;
if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == 0) {
    $arquivo = $_FILES['foto_perfil'];
    
    $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif'];
    $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
    
    if (!in_array($extensao, $extensoesPermitidas)) {
        $_SESSION['msg'] = 'Formato de imagem inválido. Use: JPG, PNG ou GIF.';
        $_SESSION['msg_tipo'] = 'erro';
        header("Location: ../pages/admin_editar_perfil.php?id=" . $idusuario_editar);
        exit;
    }
    
    if ($arquivo['size'] > 5 * 1024 * 1024) {
        $_SESSION['msg'] = 'Imagem muito grande. Máximo 5MB.';
        $_SESSION['msg_tipo'] = 'erro';
        header("Location: ../pages/admin_editar_perfil.php?id=" . $idusuario_editar);
        exit;
    }
    
    $nomeArquivo = uniqid() . '.' . $extensao;
    $pasta = '../login/uploads/';
    
    if (!is_dir($pasta)) {
        mkdir($pasta, 0755, true);
    }
    
    if (move_uploaded_file($arquivo['tmp_name'], $pasta . $nomeArquivo)) {
        $foto_perfil = $nomeArquivo;
        
        // Remove foto antiga
        $sqlFotoAntiga = "SELECT foto_perfil FROM usuarios WHERE idusuarios = ?";
        $stmt = $conexao->prepare($sqlFotoAntiga);
        $stmt->execute([$idusuario_editar]);
        $fotoAntiga = $stmt->fetchColumn();
        
        if ($fotoAntiga && file_exists($pasta . $fotoAntiga)) {
            unlink($pasta . $fotoAntiga);
        }
    }
}

//Atualiza dados no banco
try {
    $sql = "UPDATE usuarios SET nome = ?, nome_usuario = ?, email = ?, nascimento = ?";
    $params = [$nome, $nome_usuario, $email, $nascimento ?: null];
    
    // evitar erros
    if (colunaExiste($conexao, 'usuarios', 'genero')) {
        $sql .= ", genero = ?";
        $params[] = $genero ?: null;
    }
    
    if (colunaExiste($conexao, 'usuarios', 'objetivos')) {
        $sql .= ", objetivos = ?";
        $params[] = $objetivos ?: null;
    }
    
    if (colunaExiste($conexao, 'usuarios', 'descricao_pessoal')) {
        $sql .= ", descricao_pessoal = ?";
        $params[] = $descricao_pessoal ?: null;
    }
    
    if (colunaExiste($conexao, 'usuarios', 'tipo_treino_favorito')) {
        $sql .= ", tipo_treino_favorito = ?";
        $params[] = $tipo_treino_favorito ?: null;
    }
    
    if (colunaExiste($conexao, 'usuarios', 'is_admin')) {
        $sql .= ", is_admin = ?";
        $params[] = $is_admin;
    }
    
    if ($foto_perfil) {
        $sql .= ", foto_perfil = ?";
        $params[] = $foto_perfil;
    }
    
    $sql .= " WHERE idusuarios = ?";
    $params[] = $idusuario_editar;
    
    $stmt = $conexao->prepare($sql);
    $stmt->execute($params);
    
    $_SESSION['msg'] = 'Perfil atualizado com sucesso!';
    $_SESSION['msg_tipo'] = 'sucesso';
    
} catch (PDOException $e) {
    error_log("Erro ao atualizar perfil: " . $e->getMessage());
    $_SESSION['msg'] = 'Erro ao atualizar perfil. Tente novamente.';
    $_SESSION['msg_tipo'] = 'erro';
}

header("Location: ../pages/admin_editar_perfil.php?id=" . $idusuario_editar);
exit;

