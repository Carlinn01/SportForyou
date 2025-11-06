<?php
include("../login/incs/valida-sessao.php");
require_once "../login/src/ConexaoBD.php";
require_once "../login/src/CSRF.php";

header('Content-Type: application/json');

$idusuario_logado = $_SESSION['idusuarios'];
$conexao = ConexaoBD::conectar();

// Validação CSRF
$token = $_POST['csrf_token'] ?? '';
if (!CSRF::validarToken($token)) {
    echo json_encode(['success' => false, 'message' => 'Token de segurança inválido']);
    exit;
}

// Validação de ID - converte para int e valida
$conversa_id = isset($_POST['conversa_id']) ? (int)$_POST['conversa_id'] : 0;
$mensagem = htmlspecialchars(trim($_POST['mensagem'] ?? ''), ENT_QUOTES, 'UTF-8');
$anexo_url = null;

if ($conversa_id <= 0) {
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
    
    // Valida extensão
    $extensao = strtolower(pathinfo($nomeArquivo, PATHINFO_EXTENSION));
    $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'mov', 'pdf', 'doc', 'docx'];
    if (!in_array($extensao, $extensoesPermitidas)) {
        echo json_encode(['success' => false, 'message' => 'Tipo de arquivo não permitido']);
        exit;
    }
    
    // Bloqueia arquivos executáveis
    $extensoesBloqueadas = ['php', 'phtml', 'php3', 'php4', 'php5', 'phps', 'exe', 'bat', 'sh', 'js'];
    if (in_array($extensao, $extensoesBloqueadas)) {
        echo json_encode(['success' => false, 'message' => 'Tipo de arquivo não permitido']);
        exit;
    }
    
    // Valida MIME type real do arquivo
    $mimeTypeReal = null;
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeTypeReal = finfo_file($finfo, $tmpName);
        finfo_close($finfo);
    } elseif (function_exists('mime_content_type')) {
        $mimeTypeReal = mime_content_type($tmpName);
    }
    
    // Valida tipo
    $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'video/mp4', 'video/quicktime', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    if ($mimeTypeReal && !in_array($mimeTypeReal, $tiposPermitidos)) {
        echo json_encode(['success' => false, 'message' => 'Tipo de arquivo não permitido']);
        exit;
    }
    
    // Para imagens, valida conteúdo real
    if (in_array($extensao, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
        $imageInfo = @getimagesize($tmpName);
        if ($imageInfo === false) {
            echo json_encode(['success' => false, 'message' => 'Arquivo de imagem inválido']);
            exit;
        }
    }
    
    // Gera nome único e seguro (sem usar nome original)
    $novoNome = uniqid('anexo_', true) . '.' . $extensao;
    
    // Cria diretório se não existir
    $pastaAnexos = '../login/uploads/anexos/';
    if (!file_exists($pastaAnexos)) {
        mkdir($pastaAnexos, 0755, true);
    }
    
    // Previne path traversal - usa apenas o nome do arquivo
    $caminhoDestino = $pastaAnexos . basename($novoNome);
    
    // Valida que o caminho final está dentro da pasta permitida
    $caminhoReal = realpath($pastaAnexos);
    if ($caminhoReal === false) {
        $caminhoReal = realpath(dirname($pastaAnexos)) . '/anexos/';
    }
    $caminhoFinalReal = realpath(dirname($caminhoDestino)) . '/' . basename($novoNome);
    if (strpos($caminhoFinalReal, $caminhoReal) !== 0) {
        echo json_encode(['success' => false, 'message' => 'Caminho inválido']);
        exit;
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

// Verifica se o usuário tem permissão nesta conversa e identifica o outro usuário
$sqlVerifica = "SELECT idconversa, 
                CASE 
                    WHEN usuario1_id = ? THEN usuario2_id
                    ELSE usuario1_id
                END as outro_usuario_id
                FROM conversas 
                WHERE idconversa = ? AND (usuario1_id = ? OR usuario2_id = ?)";
$stmt = $conexao->prepare($sqlVerifica);
$stmt->execute([$idusuario_logado, $conversa_id, $idusuario_logado, $idusuario_logado]);
$conversa = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$conversa) {
    echo json_encode(['success' => false, 'message' => 'Conversa não encontrada ou sem permissão']);
    exit;
}

$outro_usuario_id = $conversa['outro_usuario_id'];

// Busca dados do usuário antes de criar notificação
$sqlUsuario = "SELECT nome, nome_usuario, foto_perfil FROM usuarios WHERE idusuarios = ?";
$stmt = $conexao->prepare($sqlUsuario);
$stmt->execute([$idusuario_logado]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

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

// Cria notificação para o outro usuário da conversa
require_once "../login/src/UsuarioDAO.php";
$nomeUsuario = $usuario['nome_usuario'];
$mensagemNotificacao = "@{$nomeUsuario} enviou uma mensagem";
$linkNotificacao = "../pages/mensagens.php?conversa={$conversa_id}";
UsuarioDAO::adicionarNotificacao($outro_usuario_id, 'mensagem', $mensagemNotificacao, $linkNotificacao);

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

