<?php
include "../login/incs/valida-sessao.php";
require_once "../login/src/ConexaoBD.php";

// Validação de entrada
if (!isset($_POST['texto']) || trim($_POST['texto']) === '') {
    header("Location: ../pages/home.php?erro=texto_vazio");
    exit;
}

$texto = trim($_POST['texto']);
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
        header("Location: home.php?erro=formato_invalido");
        exit;
    }
    
    // Validação de MIME type (com fallback caso finfo não esteja disponível)
    $mimeType = null;
    $ehVideo = false;
    
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $arquivo['tmp_name']);
        finfo_close($finfo);
    } elseif (function_exists('mime_content_type')) {
        $mimeType = mime_content_type($arquivo['tmp_name']);
    } else {
        // Validação alternativa usando getimagesize para imagens
        $imageInfo = @getimagesize($arquivo['tmp_name']);
        if ($imageInfo !== false) {
            $mimeType = $imageInfo['mime'];
        } else {
            // Se não for imagem, verifica se é vídeo pela extensão
            $ehVideo = in_array($extensao, ['mp4', 'mpeg', 'mov', 'avi']);
            if (!$ehVideo) {
                header("Location: ../pages/home.php?erro=formato_invalido");
                exit;
            }
        }
    }
    
    // Verifica se é vídeo
    if ($mimeType && strpos($mimeType, 'video/') === 0) {
        $ehVideo = true;
    }
    
    // Validação final do MIME type
    if ($mimeType && !in_array($mimeType, $tiposPermitidos) && !$ehVideo) {
        header("Location: ../pages/home.php?erro=formato_invalido");
        exit;
    }
    
    // Aumenta limite para vídeos (50MB)
    if ($ehVideo && $arquivo['size'] > 50 * 1024 * 1024) {
        header("Location: ../pages/home.php?erro=arquivo_grande");
        exit;
    }
    
    // Sanitiza o nome do arquivo
    $nomeSeguro = preg_replace('/[^a-zA-Z0-9._-]/', '', $arquivo['name']);
    $foto_nome = time() . '_' . uniqid() . '_' . $nomeSeguro;
    
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
