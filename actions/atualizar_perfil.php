<?php
include("../login/incs/valida-sessao.php");
require_once "../login/src/ConexaoBD.php";

$idusuario = $_SESSION['idusuarios'];
$conexao = ConexaoBD::conectar();

// Função para verificar se uma coluna existe na tabela
function colunaExiste($pdo, $tabela, $coluna) {
    try {
        $sql = "SHOW COLUMNS FROM $tabela LIKE '$coluna'";
        $stmt = $pdo->query($sql);
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        return false;
    }
}

// Processa atualização do perfil
$nome = $_POST['nome'] ?? '';
$nome_usuario = $_POST['nome_usuario'] ?? '';
$email = $_POST['email'] ?? '';
$nascimento = $_POST['nascimento'] ?? '';
$genero = $_POST['genero'] ?? '';
$objetivos = $_POST['objetivos'] ?? '';
$esportes_favoritos = isset($_POST['esportes_favoritos']) ? json_encode($_POST['esportes_favoritos']) : '[]';

// Processa upload de foto
$foto_perfil = null;
if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == 0) {
    $arquivo = $_FILES['foto_perfil'];
    
    // Validações
    $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif'];
    $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
    
    if (!in_array($extensao, $extensoesPermitidas)) {
        $_SESSION['msg'] = 'Formato de imagem inválido. Use: JPG, PNG ou GIF.';
        $_SESSION['msg_tipo'] = 'erro';
        header("Location: ../pages/perfil.php?id=" . $idusuario);
        exit;
    }
    
    if ($arquivo['size'] > 5 * 1024 * 1024) { // 5MB
        $_SESSION['msg'] = 'Imagem muito grande. Máximo 5MB.';
        $_SESSION['msg_tipo'] = 'erro';
        header("Location: ../pages/perfil.php?id=" . $idusuario);
        exit;
    }
    
    // Gera nome único para o arquivo
    $nomeArquivo = uniqid() . '.' . $extensao;
    $pasta = '../login/uploads/';
    
    if (!is_dir($pasta)) {
        mkdir($pasta, 0755, true);
    }
    
    if (move_uploaded_file($arquivo['tmp_name'], $pasta . $nomeArquivo)) {
        $foto_perfil = $nomeArquivo;
        
        // Remove foto antiga (se houver)
        $sqlFotoAntiga = "SELECT foto_perfil FROM usuarios WHERE idusuarios = ?";
        $stmt = $conexao->prepare($sqlFotoAntiga);
        $stmt->execute([$idusuario]);
        $fotoAntiga = $stmt->fetchColumn();
        
        if ($fotoAntiga && file_exists($pasta . $fotoAntiga)) {
            unlink($pasta . $fotoAntiga);
        }
    }
}

// Atualiza dados no banco (sempre atualiza campos básicos)
$sql = "UPDATE usuarios SET nome = ?, nome_usuario = ?, email = ?, nascimento = ?";
$params = [$nome, $nome_usuario, $email, $nascimento];

// Adiciona gênero apenas se a coluna existir e o valor foi informado
if ($genero && colunaExiste($conexao, 'usuarios', 'genero')) {
    $sql .= ", genero = ?";
    $params[] = $genero;
}

// Adiciona objetivos apenas se a coluna existir
if ($objetivos !== '' && colunaExiste($conexao, 'usuarios', 'objetivos')) {
    $sql .= ", objetivos = ?";
    $params[] = $objetivos;
}

// Adiciona esportes favoritos apenas se a coluna existir
if ($esportes_favoritos !== '[]' && colunaExiste($conexao, 'usuarios', 'esportes_favoritos')) {
    $sql .= ", esportes_favoritos = ?";
    $params[] = $esportes_favoritos;
}

// Adiciona foto se foi enviada
if ($foto_perfil) {
    $sql .= ", foto_perfil = ?";
    $params[] = $foto_perfil;
}

$sql .= " WHERE idusuarios = ?";
$params[] = $idusuario;

try {
    $stmt = $conexao->prepare($sql);
    $stmt->execute($params);
    
    // Atualiza a sessão
    $_SESSION['nome'] = $nome;
    $_SESSION['nome_usuario'] = $nome_usuario;
    $_SESSION['email'] = $email;
    if ($foto_perfil) {
        $_SESSION['foto_perfil'] = $foto_perfil;
    }
    
    $_SESSION['msg'] = 'Perfil atualizado com sucesso!';
    $_SESSION['msg_tipo'] = 'sucesso';
} catch (PDOException $e) {
    $_SESSION['msg'] = 'Erro ao atualizar perfil: ' . $e->getMessage();
    $_SESSION['msg_tipo'] = 'erro';
}

header("Location: ../pages/perfil.php?id=" . $idusuario);
exit;
