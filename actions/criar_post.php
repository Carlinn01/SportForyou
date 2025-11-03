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
    
    // Validação de tipo de arquivo permitido
    $tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    // Validação de tamanho (máximo 5MB)
    $tamanhoMaximo = 5 * 1024 * 1024; // 5MB em bytes
    
    if ($arquivo['size'] > $tamanhoMaximo) {
        header("Location: home.php?erro=arquivo_grande");
        exit;
    }
    
    // Validação de extensão
    $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
    if (!in_array($extensao, $extensoesPermitidas)) {
        header("Location: home.php?erro=formato_invalido");
        exit;
    }
    
    // Validação de MIME type (com fallback caso finfo não esteja disponível)
    $mimeType = null;
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $arquivo['tmp_name']);
        finfo_close($finfo);
    } elseif (function_exists('mime_content_type')) {
        $mimeType = mime_content_type($arquivo['tmp_name']);
    } else {
        // Validação alternativa usando getimagesize para verificar se é imagem válida
        $imageInfo = @getimagesize($arquivo['tmp_name']);
        if ($imageInfo !== false) {
            $mimeType = $imageInfo['mime'];
        } else {
            header("Location: home.php?erro=formato_invalido");
            exit;
        }
    }
    
    // Validação final do MIME type
    if ($mimeType && !in_array($mimeType, $tiposPermitidos)) {
        header("Location: home.php?erro=formato_invalido");
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
$sql = "INSERT INTO postagens (idusuario, texto, foto, criado_em) VALUES (?, ?, ?, NOW())";
$stmt = $conexao->prepare($sql);
$stmt->bindParam(1, $idusuario);
$stmt->bindParam(2, $texto);
$stmt->bindParam(3, $foto_nome);
$stmt->execute();

header("Location: ../pages/home.php");
exit;
?>
