<?php
include("../login/incs/valida-sessao.php");
require_once "../login/src/ConexaoBD.php";

$idusuario = $_SESSION['idusuarios'];
$conexao = ConexaoBD::conectar();

// Garante que a conexão usa UTF-8
try {
    $conexao->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
} catch (PDOException $e) {
    // Ignora se falhar
}

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

// Remove valores vazios do array (do campo hidden)
if (is_array($esportes_favoritos)) {
    $esportes_favoritos = array_filter($esportes_favoritos, function($valor) {
        return $valor !== '';
    });
    $esportes_favoritos = array_values($esportes_favoritos); // Reindexa o array
}

// Garante que é um array
if (!is_array($esportes_favoritos)) {
    $esportes_favoritos = [];
}

// Processa esportes detalhados (nível e frequência)
$esportes_detalhados = [];
if (is_array($esportes_favoritos) && !empty($esportes_favoritos)) {
    foreach ($esportes_favoritos as $esporte) {
        $nivelKey = 'nivel_' . $esporte;
        $frequenciaKey = 'frequencia_' . $esporte;
        
        $nivel = isset($_POST[$nivelKey]) ? trim($_POST[$nivelKey]) : '';
        $frequencia = isset($_POST[$frequenciaKey]) ? trim($_POST[$frequenciaKey]) : '';
        
        // Se tiver nível ou frequência, adiciona aos detalhados
        if ($nivel || $frequencia) {
            $esportes_detalhados[] = [
                'esporte' => $esporte,
                'nivel' => $nivel,
                'frequencia' => $frequencia
            ];
        }
    }
}

$esportes_favoritos_json = json_encode($esportes_favoritos, JSON_UNESCAPED_UNICODE);
$esportes_detalhados_json = json_encode($esportes_detalhados, JSON_UNESCAPED_UNICODE);

// Se não houver esportes favoritos, salva array vazio
if (empty($esportes_favoritos)) {
    $esportes_favoritos_json = '[]';
}

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

// Verifica se o nome de usuário já existe (exceto para o próprio usuário)
$nome_usuario_trimmed = trim($nome_usuario);
if ($nome_usuario_trimmed) {
    // Primeiro verifica se o nome de usuário atual do usuário é diferente do novo
    $sqlUsuarioAtual = "SELECT nome_usuario FROM usuarios WHERE idusuarios = ?";
    $stmtUsuarioAtual = $conexao->prepare($sqlUsuarioAtual);
    $stmtUsuarioAtual->execute([$idusuario]);
    $nome_usuario_atual = $stmtUsuarioAtual->fetchColumn();
    
    // Só verifica se o nome de usuário mudou
    if ($nome_usuario_trimmed !== $nome_usuario_atual) {
        $sqlVerificar = "SELECT idusuarios FROM usuarios WHERE nome_usuario = ? AND idusuarios != ?";
        $stmtVerificar = $conexao->prepare($sqlVerificar);
        $stmtVerificar->execute([$nome_usuario_trimmed, $idusuario]);
        
        if ($stmtVerificar->rowCount() > 0) {
            $_SESSION['msg'] = 'Este nome de usuário já está em uso. Por favor, escolha outro.';
            $_SESSION['msg_tipo'] = 'erro';
            header("Location: ../pages/perfil.php?id=" . $idusuario);
            exit;
        }
    }
}

// Validações básicas
if (empty($nome) || empty($nome_usuario_trimmed) || empty($email)) {
    $_SESSION['msg'] = 'Nome, nome de usuário e email são obrigatórios.';
    $_SESSION['msg_tipo'] = 'erro';
    header("Location: ../pages/perfil.php?id=" . $idusuario);
    exit;
}

// Valida email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['msg'] = 'Email inválido.';
    $_SESSION['msg_tipo'] = 'erro';
    header("Location: ../pages/perfil.php?id=" . $idusuario);
    exit;
}

// Atualiza dados no banco (sempre atualiza campos básicos)
$sql = "UPDATE usuarios SET nome = ?, nome_usuario = ?, email = ?, nascimento = ?";
$params = [$nome, $nome_usuario_trimmed, $email, $nascimento];

// Adiciona gênero apenas se a coluna existir e o valor foi informado
if ($genero && colunaExiste($conexao, 'usuarios', 'genero')) {
    $sql .= ", genero = ?";
    $params[] = $genero;
}

// Adiciona objetivos apenas se a coluna existir (sempre atualiza, mesmo se vazio)
if (colunaExiste($conexao, 'usuarios', 'objetivos')) {
    $sql .= ", objetivos = ?";
    $params[] = $objetivos;
}

// Adiciona esportes favoritos apenas se a coluna existir (sempre atualiza, mesmo se vazio)
// Tenta criar a coluna se não existir
if (!colunaExiste($conexao, 'usuarios', 'esportes_favoritos')) {
    try {
        $conexao->exec("ALTER TABLE usuarios ADD COLUMN esportes_favoritos TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL");
    } catch (PDOException $e) {
        // Ignora se já existir
    }
} else {
    // Se a coluna já existe, tenta corrigir o encoding
    try {
        $conexao->exec("ALTER TABLE usuarios MODIFY COLUMN esportes_favoritos TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL");
    } catch (PDOException $e) {
        // Ignora se falhar
    }
}
if (colunaExiste($conexao, 'usuarios', 'esportes_favoritos')) {
    $sql .= ", esportes_favoritos = ?";
    $params[] = $esportes_favoritos_json;
}

// Adiciona descrição pessoal se a coluna existir (tenta criar se não existir)
if (!colunaExiste($conexao, 'usuarios', 'descricao_pessoal')) {
    try {
        $conexao->exec("ALTER TABLE usuarios ADD COLUMN descricao_pessoal TEXT DEFAULT NULL AFTER objetivos");
    } catch (PDOException $e) {
        // Ignora se já existir
    }
}
if (colunaExiste($conexao, 'usuarios', 'descricao_pessoal')) {
    $sql .= ", descricao_pessoal = ?";
    $params[] = $descricao_pessoal;
}

// Adiciona tipo de treino favorito se a coluna existir (tenta criar se não existir)
if (!colunaExiste($conexao, 'usuarios', 'tipo_treino_favorito')) {
    try {
        // Tenta criar após descricao_pessoal
        $conexao->exec("ALTER TABLE usuarios ADD COLUMN tipo_treino_favorito VARCHAR(100) DEFAULT NULL AFTER descricao_pessoal");
    } catch (PDOException $e) {
        // Se falhar, tenta criar sem posição específica
        try {
            $conexao->exec("ALTER TABLE usuarios ADD COLUMN tipo_treino_favorito VARCHAR(100) DEFAULT NULL");
        } catch (PDOException $e2) {
            // Ignora se já existir
        }
    }
}
if (colunaExiste($conexao, 'usuarios', 'tipo_treino_favorito')) {
    $sql .= ", tipo_treino_favorito = ?";
    $params[] = $tipo_treino_favorito;
}

// Adiciona esportes detalhados se a coluna existir (sempre atualiza, mesmo se vazio)
// Tenta criar a coluna se não existir
if (!colunaExiste($conexao, 'usuarios', 'esportes_detalhados')) {
    try {
        // Tenta criar após esportes_favoritos com charset UTF-8
        $conexao->exec("ALTER TABLE usuarios ADD COLUMN esportes_detalhados TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER esportes_favoritos");
    } catch (PDOException $e) {
        // Se falhar, tenta criar sem posição específica
        try {
            $conexao->exec("ALTER TABLE usuarios ADD COLUMN esportes_detalhados TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL");
        } catch (PDOException $e2) {
            // Ignora se já existir ou outro erro
        }
    }
} else {
    // Se a coluna já existe, tenta corrigir o encoding
    try {
        $conexao->exec("ALTER TABLE usuarios MODIFY COLUMN esportes_detalhados TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL");
    } catch (PDOException $e) {
        // Ignora se falhar
    }
}
if (colunaExiste($conexao, 'usuarios', 'esportes_detalhados')) {
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
    // Verifica se o SQL está vazio (sem campos para atualizar)
    if (trim($sql) === "UPDATE usuarios SET" || trim($sql) === "UPDATE usuarios SET WHERE idusuarios = ?") {
        $_SESSION['msg'] = 'Erro: Nenhum campo para atualizar.';
        $_SESSION['msg_tipo'] = 'erro';
        header("Location: ../pages/perfil.php?id=" . $idusuario);
        exit;
    }
    
    $stmt = $conexao->prepare($sql);
    $resultado = $stmt->execute($params);
    
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
    error_log("Erro ao atualizar perfil: " . $e->getMessage());
    $_SESSION['msg'] = 'Erro ao atualizar perfil. Tente novamente.';
    $_SESSION['msg_tipo'] = 'erro';
}

header("Location: ../pages/perfil.php?id=" . $idusuario);
exit;
