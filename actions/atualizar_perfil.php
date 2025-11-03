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
$descricao_pessoal = $_POST['descricao_pessoal'] ?? '';
$tipo_treino_favorito = $_POST['tipo_treino_favorito'] ?? '';

// Processa esportes favoritos
$esportes_favoritos = isset($_POST['esportes_favoritos']) ? $_POST['esportes_favoritos'] : [];

// Processa esportes detalhados (nível e frequência)
$esportes_detalhados = [];
foreach ($esportes_favoritos as $esporte) {
    $nivel = $_POST['nivel_' . $esporte] ?? '';
    $frequencia = $_POST['frequencia_' . $esporte] ?? '';
    if ($nivel || $frequencia) {
        $esportes_detalhados[] = [
            'esporte' => $esporte,
            'nivel' => $nivel,
            'frequencia' => $frequencia
        ];
    }
}

// Processa esportes personalizados
if (isset($_POST['esportes_personalizados_nome']) && is_array($_POST['esportes_personalizados_nome'])) {
    $nomes = $_POST['esportes_personalizados_nome'];
    $niveis = $_POST['esportes_personalizados_nivel'] ?? [];
    $frequencias = $_POST['esportes_personalizados_frequencia'] ?? [];
    
    foreach ($nomes as $index => $nome) {
        $nome = trim($nome);
        if (!empty($nome)) {
            $esportes_favoritos[] = $nome; // Adiciona aos favoritos
            $esportes_detalhados[] = [
                'esporte' => $nome,
                'nivel' => $niveis[$index] ?? '',
                'frequencia' => $frequencias[$index] ?? ''
            ];
        }
    }
}

$esportes_favoritos_json = json_encode($esportes_favoritos);
$esportes_detalhados_json = json_encode($esportes_detalhados);

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
if ($esportes_favoritos_json !== '[]' && colunaExiste($conexao, 'usuarios', 'esportes_favoritos')) {
    $sql .= ", esportes_favoritos = ?";
    $params[] = $esportes_favoritos_json;
}

// Adiciona descrição pessoal se a coluna existir
if ($descricao_pessoal !== '' && colunaExiste($conexao, 'usuarios', 'descricao_pessoal')) {
    $sql .= ", descricao_pessoal = ?";
    $params[] = $descricao_pessoal;
}

// Adiciona tipo de treino favorito se a coluna existir
if ($tipo_treino_favorito !== '' && colunaExiste($conexao, 'usuarios', 'tipo_treino_favorito')) {
    $sql .= ", tipo_treino_favorito = ?";
    $params[] = $tipo_treino_favorito;
}

// Adiciona esportes detalhados se a coluna existir
if ($esportes_detalhados_json !== '[]' && colunaExiste($conexao, 'usuarios', 'esportes_detalhados')) {
    $sql .= ", esportes_detalhados = ?";
    $params[] = $esportes_detalhados_json;
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
