<?php
include("login/incs/valida-sessao.php");
require_once "login/src/ConexaoBD.php";

header('Content-Type: application/json');

$idusuario_logado = $_SESSION['idusuarios'];
$conexao = ConexaoBD::conectar();

$conversa_id = $_POST['conversa_id'] ?? null;
$mensagem = trim($_POST['mensagem'] ?? '');
$anexo_url = null;

if (!$conversa_id || !is_numeric($conversa_id)) {
    echo json_encode(['success' => false, 'message' => 'ID de conversa inválido']);
    exit;
}

// Verifica se há anexo
if (isset($_FILES['anexo']) && $_FILES['anexo']['error'] === UPLOAD_ERR_OK) {
    $arquivo = $_FILES['anexo'];
    $nomeArquivo = $arquivo['name'];
    $tipoArquivo = $arquivo['type'];
    $tamanhoArquivo = $arquivo['size'];
    $tmpName = $arquivo['tmp_name'];
    
    // Valida tamanho (máximo 10MB)
    $maxSize = 10 * 1024 * 1024;
    if ($tamanhoArquivo > $maxSize) {
        echo json_encode(['success' => false, 'message' => 'Arquivo muito grande. Máximo: 10MB']);
        exit;
    }
    
    // Valida tipo
    $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'video/mp4', 'video/quicktime', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    if (!in_array($tipoArquivo, $tiposPermitidos)) {
        echo json_encode(['success' => false, 'message' => 'Tipo de arquivo não permitido']);
        exit;
    }
    
    // Gera nome único
    $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
    $novoNome = uniqid('anexo_', true) . '.' . $extensao;
    $caminhoDestino = 'login/uploads/anexos/' . $novoNome;
    
    // Cria diretório se não existir
    if (!file_exists('login/uploads/anexos')) {
        mkdir('login/uploads/anexos', 0755, true);
    }
    
    // Move arquivo
    if (move_uploaded_file($tmpName, $caminhoDestino)) {
        $anexo_url = 'anexos/' . $novoNome;
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao fazer upload do arquivo']);
        exit;
    }
}

if (empty($mensagem) && !$anexo_url) {
    echo json_encode(['success' => false, 'message' => 'Mensagem não pode estar vazia']);
    exit;
}

$conversa_id = (int)$conversa_id;

// Verifica se o usuário tem permissão nesta conversa
$sqlVerifica = "SELECT idconversa FROM conversas WHERE idconversa = ? AND (usuario1_id = ? OR usuario2_id = ?)";
$stmt = $conexao->prepare($sqlVerifica);
$stmt->execute([$conversa_id, $idusuario_logado, $idusuario_logado]);
if (!$stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Conversa não encontrada ou sem permissão']);
    exit;
}

// Insere a mensagem
$sql = "INSERT INTO mensagens (conversa_id, remetente_id, conteudo, status, anexo_url) VALUES (?, ?, ?, 'enviada', ?)";
$stmt = $conexao->prepare($sql);
$conteudo = $mensagem ?: ($anexo_url ? '📎 Anexo enviado' : '');
$stmt->execute([$conversa_id, $idusuario_logado, $conteudo, $anexo_url]);

$idmensagem = $conexao->lastInsertId();

// Atualiza a última mensagem da conversa
$sqlUpdate = "UPDATE conversas SET ultima_mensagem_id = ?, atualizado_em = NOW() WHERE idconversa = ?";
$stmt = $conexao->prepare($sqlUpdate);
$stmt->execute([$idmensagem, $conversa_id]);

// Marca como entregue (atualiza status)
$sqlStatus = "UPDATE mensagens SET status = 'entregue' WHERE idmensagem = ?";
$stmt = $conexao->prepare($sqlStatus);
$stmt->execute([$idmensagem]);

// Busca dados do usuário para retornar
$sqlUsuario = "SELECT nome, nome_usuario, foto_perfil FROM usuarios WHERE idusuarios = ?";
$stmt = $conexao->prepare($sqlUsuario);
$stmt->execute([$idusuario_logado]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'mensagem' => [
        'id' => $idmensagem,
        'conteudo' => htmlspecialchars($conteudo),
        'remetente_id' => $idusuario_logado,
        'nome' => $usuario['nome'],
        'nome_usuario' => $usuario['nome_usuario'],
        'foto_perfil' => $usuario['foto_perfil'],
        'status' => 'entregue',
        'anexo_url' => $anexo_url,
        'hora' => date('H:i'),
        'data' => date('Y-m-d H:i:s')
    ]
]);
exit;

