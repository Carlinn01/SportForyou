<?php
include "../login/incs/valida-sessao.php";
require_once "../login/src/ConexaoBD.php";
require_once "../login/src/CSRF.php";

// Validação CSRF
$token = $_POST['csrf_token'] ?? '';
if (!CSRF::validarToken($token)) {
    $_SESSION['msg'] = 'Token de segurança inválido.';
    $_SESSION['msg_tipo'] = 'erro';
    header("Location: ../pages/home.php");
    exit;
}

// Validação de entrada
if (!isset($_POST['texto']) || trim($_POST['texto']) === '') {
    header("Location: ../pages/home.php?erro=texto_vazio");
    exit;
}

$texto = htmlspecialchars(trim($_POST['texto']), ENT_QUOTES, 'UTF-8');
$idusuario = $_SESSION['idusuarios'];
$foto_nome = null;

// Verifica se enviou imagem e valida
if(isset($_FILES['foto']) && $_FILES['foto']['error'] == 0){
    $arquivo = $_FILES['foto'];
    
    // Validação de tipo de arquivo permitido (imagens e vídeos)
    $tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'video/mp4', 'video/mpeg', 'video/quicktime', 'video/x-msvideo'];
    $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'mpeg', 'mov', 'avi'];
    
    // Validação de tamanho (máximo 5MB para imagens, 50MB para vídeos)
    // Será verificado depois de determinar o tipo
    
    // Validação de extensão
    $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
    if (!in_array($extensao, $extensoesPermitidas)) {
        header("Location: ../pages/home.php?erro=formato_invalido");
        exit;
    }
    
    // Validação de MIME type real do arquivo (não confia no que o cliente envia)
    $mimeType = null;
    $ehVideo = false;
    $ehImagem = false;
    
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $arquivo['tmp_name']);
        finfo_close($finfo);
    } elseif (function_exists('mime_content_type')) {
        $mimeType = mime_content_type($arquivo['tmp_name']);
    }
    
    // Validação adicional: verifica conteúdo real do arquivo
    $imageInfo = @getimagesize($arquivo['tmp_name']);
    if ($imageInfo !== false) {
        $ehImagem = true;
        $mimeType = $imageInfo['mime'];
        
        // Verifica se o MIME type corresponde à extensão
        $mimeEsperado = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp'
        ];
        
        if (isset($mimeEsperado[$extensao]) && $mimeType !== $mimeEsperado[$extensao]) {
            header("Location: ../pages/home.php?erro=formato_invalido");
            exit;
        }
    } else {
        // Se não é imagem, verifica se é vídeo
        if (in_array($extensao, ['mp4', 'mpeg', 'mov', 'avi'])) {
            $ehVideo = true;
            // Para vídeos, confia no MIME type detectado
            if ($mimeType && strpos($mimeType, 'video/') !== 0) {
                header("Location: ../pages/home.php?erro=formato_invalido");
                exit;
            }
        } else {
            header("Location: ../pages/home.php?erro=formato_invalido");
            exit;
        }
    }
    
    // Verifica se é vídeo pelo MIME type
    if ($mimeType && strpos($mimeType, 'video/') === 0) {
        $ehVideo = true;
        $ehImagem = false;
    }
    
    // Validação final do MIME type
    if ($mimeType && !in_array($mimeType, $tiposPermitidos) && !$ehVideo && !$ehImagem) {
        header("Location: ../pages/home.php?erro=formato_invalido");
        exit;
    }
    
    // Bloqueia upload de arquivos PHP ou executáveis
    $extensoesBloqueadas = ['php', 'phtml', 'php3', 'php4', 'php5', 'phps', 'exe', 'bat', 'sh', 'js'];
    if (in_array($extensao, $extensoesBloqueadas)) {
        header("Location: ../pages/home.php?erro=formato_invalido");
        exit;
    }
    
    // Aumenta limite para vídeos (50MB), imagens (5MB)
    $limiteMaximo = $ehVideo ? (50 * 1024 * 1024) : (5 * 1024 * 1024);
    if ($arquivo['size'] > $limiteMaximo) {
        header("Location: ../pages/home.php?erro=arquivo_grande");
        exit;
    }
    
    // Gera nome único e seguro (sem usar nome original)
    $foto_nome = uniqid('post_', true) . '.' . $extensao;
    
    $pasta = '../login/uploads/';
    
    // Garante que a pasta existe
    if (!is_dir($pasta)) {
        mkdir($pasta, 0755, true);
    }
    
    if (!move_uploaded_file($arquivo['tmp_name'], $pasta . $foto_nome)) {
        header("Location: home.php?erro=upload_falhou");
        exit;
    }
}

$conexao = ConexaoBD::conectar();

// Determina o tipo de mídia (imagem ou vídeo)
$tipo_media = null;
if ($foto_nome) {
    $tipo_media = $ehVideo ? 'video' : 'imagem';
}

// Verifica se a coluna tipo existe antes de incluir
try {
    $sqlCheck = "SHOW COLUMNS FROM postagens LIKE 'tipo'";
    $stmtCheck = $conexao->query($sqlCheck);
    $temTipo = $stmtCheck->rowCount() > 0;
} catch (PDOException $e) {
    $temTipo = false;
}

if ($temTipo) {
    $sql = "INSERT INTO postagens (idusuario, texto, foto, tipo, criado_em) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(1, $idusuario);
    $stmt->bindParam(2, $texto);
    $stmt->bindParam(3, $foto_nome);
    $stmt->bindParam(4, $tipo_media);
} else {
    $sql = "INSERT INTO postagens (idusuario, texto, foto, criado_em) VALUES (?, ?, ?, NOW())";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(1, $idusuario);
    $stmt->bindParam(2, $texto);
    $stmt->bindParam(3, $foto_nome);
}
$stmt->execute();

header("Location: ../pages/home.php");
exit;
?>
