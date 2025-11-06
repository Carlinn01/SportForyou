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
error_log("=== DEBUG ESPORTES FAVORITOS ===");
error_log("POST['esportes_favoritos'] existe? " . (isset($_POST['esportes_favoritos']) ? 'SIM' : 'NÃO'));
if (isset($_POST['esportes_favoritos'])) {
    error_log("Tipo: " . gettype($_POST['esportes_favoritos']));
    error_log("Valor: " . print_r($_POST['esportes_favoritos'], true));
}

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
    error_log("esportes_favoritos não é array, convertendo...");
    $esportes_favoritos = [];
}

error_log("Esportes favoritos processados: " . print_r($esportes_favoritos, true));
error_log("Total de esportes: " . count($esportes_favoritos));

// Processa esportes detalhados (nível e frequência)
$esportes_detalhados = [];
if (is_array($esportes_favoritos) && !empty($esportes_favoritos)) {
    error_log("=== PROCESSANDO ESPORTES DETALHADOS ===");
    error_log("Esportes favoritos: " . print_r($esportes_favoritos, true));
    error_log("POST completo: " . print_r($_POST, true));
    
    foreach ($esportes_favoritos as $esporte) {
        // Tenta buscar o nível e frequência de diferentes formas
        $nivelKey = 'nivel_' . $esporte;
        $frequenciaKey = 'frequencia_' . $esporte;
        
        $nivel = isset($_POST[$nivelKey]) ? trim($_POST[$nivelKey]) : '';
        $frequencia = isset($_POST[$frequenciaKey]) ? trim($_POST[$frequenciaKey]) : '';
        
        error_log("Esporte: $esporte");
        error_log("  - Chave nível: $nivelKey");
        error_log("  - Chave frequência: $frequenciaKey");
        error_log("  - Nível recebido: " . ($nivel ?: 'VAZIO'));
        error_log("  - Frequência recebida: " . ($frequencia ?: 'VAZIO'));
        
        // Se tiver nível ou frequência, adiciona aos detalhados
        if ($nivel || $frequencia) {
            $esportes_detalhados[] = [
                'esporte' => $esporte,
                'nivel' => $nivel,
                'frequencia' => $frequencia
            ];
            error_log("  - Adicionado aos detalhados");
        } else {
            error_log("  - NÃO adicionado (sem nível nem frequência)");
        }
    }
    
    error_log("Total de esportes detalhados: " . count($esportes_detalhados));
}

$esportes_favoritos_json = json_encode($esportes_favoritos, JSON_UNESCAPED_UNICODE);
$esportes_detalhados_json = json_encode($esportes_detalhados, JSON_UNESCAPED_UNICODE);

error_log("JSON esportes_favoritos: " . $esportes_favoritos_json);
error_log("JSON esportes_detalhados: " . $esportes_detalhados_json);

// Se não houver esportes favoritos, salva array vazio
if (empty($esportes_favoritos)) {
    $esportes_favoritos_json = '[]';
    error_log("⚠️ Nenhum esporte favorito selecionado - salvando array vazio");
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

// Atualiza dados no banco (sempre atualiza campos básicos)
$sql = "UPDATE usuarios SET nome = ?, nome_usuario = ?, email = ?, nascimento = ?";
$params = [$nome, $nome_usuario_trimmed, $email, $nascimento];

error_log("=== INICIANDO ATUALIZAÇÃO ===");
error_log("Nome: $nome");
error_log("Nome usuário: $nome_usuario_trimmed");
error_log("Email: $email");
error_log("Nascimento: $nascimento");
error_log("Objetivos recebido: " . ($objetivos ?? 'NULL'));

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
        $conexao->exec("ALTER TABLE usuarios ADD COLUMN esportes_favoritos TEXT DEFAULT NULL");
    } catch (PDOException $e) {
        // Ignora se já existir
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
        // Tenta criar após esportes_favoritos
        $conexao->exec("ALTER TABLE usuarios ADD COLUMN esportes_detalhados TEXT DEFAULT NULL AFTER esportes_favoritos");
    } catch (PDOException $e) {
        // Se falhar, tenta criar sem posição específica
        try {
            $conexao->exec("ALTER TABLE usuarios ADD COLUMN esportes_detalhados TEXT DEFAULT NULL");
        } catch (PDOException $e2) {
            // Ignora se já existir ou outro erro
        }
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
    // Debug: log dos dados antes de salvar
    error_log("=== DEBUG ATUALIZAR PERFIL ===");
    error_log("Esportes favoritos: " . print_r($esportes_favoritos, true));
    error_log("Esportes favoritos JSON: " . $esportes_favoritos_json);
    error_log("Esportes detalhados: " . print_r($esportes_detalhados, true));
    error_log("Esportes detalhados JSON: " . $esportes_detalhados_json);
    error_log("Objetivos: " . $objetivos);
    error_log("SQL: " . $sql);
    error_log("Params count: " . count($params));
    
    // Verifica se o SQL está vazio (sem campos para atualizar)
    if (trim($sql) === "UPDATE usuarios SET" || trim($sql) === "UPDATE usuarios SET WHERE idusuarios = ?") {
        error_log("ERRO: SQL vazio - nenhum campo para atualizar!");
        $_SESSION['msg'] = 'Erro: Nenhum campo para atualizar. Verifique os logs.';
        $_SESSION['msg_tipo'] = 'erro';
        header("Location: ../pages/perfil.php?id=" . $idusuario);
        exit;
    }
    
    $stmt = $conexao->prepare($sql);
    $resultado = $stmt->execute($params);
    
    error_log("Resultado execute: " . ($resultado ? 'true' : 'false'));
    error_log("Row count: " . $stmt->rowCount());
    
    // Verifica se realmente atualizou
    if ($stmt->rowCount() > 0 || true) { // Sempre mostra sucesso mesmo se não alterou nada
        // Atualiza a sessão
        $_SESSION['nome'] = $nome;
        $_SESSION['nome_usuario'] = $nome_usuario;
        $_SESSION['email'] = $email;
        if ($foto_perfil) {
            $_SESSION['foto_perfil'] = $foto_perfil;
        }
        
        $_SESSION['msg'] = 'Perfil atualizado com sucesso!';
        $_SESSION['msg_tipo'] = 'sucesso';
        
        // Debug: verifica o que foi salvo
        $sqlCheck = "SELECT esportes_favoritos, esportes_detalhados, objetivos FROM usuarios WHERE idusuarios = ?";
        $stmtCheck = $conexao->prepare($sqlCheck);
        $stmtCheck->execute([$idusuario]);
        $check = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        error_log("=== DADOS SALVOS NO BANCO ===");
        error_log("esportes_favoritos: " . ($check['esportes_favoritos'] ?? 'NULL'));
        error_log("esportes_detalhados: " . ($check['esportes_detalhados'] ?? 'NULL'));
        error_log("objetivos: " . ($check['objetivos'] ?? 'NULL'));
        
        // Compara o que foi enviado com o que foi salvo
        error_log("=== COMPARAÇÃO ===");
        error_log("Enviado (JSON): " . $esportes_favoritos_json);
        error_log("Salvo no banco: " . ($check['esportes_favoritos'] ?? 'NULL'));
        if ($esportes_favoritos_json !== ($check['esportes_favoritos'] ?? '')) {
            error_log("⚠️ ATENÇÃO: Os dados enviados são diferentes dos dados salvos!");
        } else {
            error_log("✅ Os dados foram salvos corretamente.");
        }
    } else {
        $_SESSION['msg'] = 'Nenhuma alteração foi feita.';
        $_SESSION['msg_tipo'] = 'info';
    }
} catch (PDOException $e) {
    error_log("Erro ao atualizar perfil: " . $e->getMessage());
    error_log("SQL: " . $sql);
    error_log("Params: " . print_r($params, true));
    $_SESSION['msg'] = 'Erro ao atualizar perfil: ' . $e->getMessage();
    $_SESSION['msg_tipo'] = 'erro';
}

header("Location: ../pages/perfil.php?id=" . $idusuario);
exit;
