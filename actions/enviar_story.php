<?php
include "../login/incs/valida-sessao.php"; // já garante sessão e redireciona se não estiver logado
require_once "../login/src/ConexaoBD.php";

$idusuario_logado = $_SESSION['idusuarios']; // nome correto da sessão
$pdo = ConexaoBD::conectar();

if (isset($_FILES['story'])) {
    $arquivo = $_FILES['story'];

    if ($arquivo['error'] === UPLOAD_ERR_OK) {
        // Validação de tipo e tamanho
        $tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'video/mp4', 'video/mov', 'video/avi', 'video/quicktime'];
        $extensoesImagem = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $extensoesVideo = ['mp4', 'mov', 'avi'];
        $tamanhoMaximo = 10 * 1024 * 1024; // 10MB
        
        // Validação de tamanho
        if ($arquivo['size'] > $tamanhoMaximo) {
            header("Location: ../pages/home.php?erro=arquivo_grande");
            exit;
        }
        
        $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
        
        // Validação de extensão
        $tiposPermitidosExt = array_merge($extensoesImagem, $extensoesVideo);
        if (!in_array($extensao, $tiposPermitidosExt)) {
            header("Location: ../pages/home.php?erro=formato_invalido");
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
            // Validação alternativa baseada em extensão e getimagesize para imagens
            if (in_array($extensao, $extensoesImagem)) {
                $imageInfo = @getimagesize($arquivo['tmp_name']);
                if ($imageInfo !== false) {
                    $mimeType = $imageInfo['mime'];
                } else {
                    header("Location: ../pages/home.php?erro=formato_invalido");
                    exit;
                }
            } elseif (in_array($extensao, $extensoesVideo)) {
                // Para vídeos, confiamos na extensão já validada
                $mimeType = 'video/' . ($extensao === 'mov' ? 'quicktime' : $extensao);
            }
        }
        
        // Validação final do MIME type se conseguiu detectar
        if ($mimeType && !in_array($mimeType, $tiposPermitidos)) {
            header("Location: ../pages/home.php?erro=formato_invalido");
            exit;
        }
        
        // Sanitiza o nome do arquivo
        $nomeSeguro = preg_replace('/[^a-zA-Z0-9._-]/', '', $arquivo['name']);
        $nome_arquivo = uniqid() . '_' . time() . '.' . $extensao;
        $pasta = '../login/uploads/stories/';

        // Cria a pasta se não existir com permissões seguras
        if (!is_dir($pasta)) {
            mkdir($pasta, 0755, true);
        }

        $caminho = $pasta . $nome_arquivo;
        
        // Caminho relativo para salvar no banco (sem o ../login/uploads inicial)
        $midia_relativa = 'stories/' . $nome_arquivo;

        // Move o arquivo enviado
        if (move_uploaded_file($arquivo['tmp_name'], $caminho)) {
            // Determina o tipo de mídia
            $tipo = in_array($extensao, $extensoesVideo) ? 'video' : 'imagem';

            // Insere no banco de dados (salva apenas o caminho relativo)
            $sql = "INSERT INTO stories (idusuario, midia, tipo) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$idusuario_logado, $midia_relativa, $tipo]);

            header("Location: ../pages/home.php");
            exit;
        } else {
            header("Location: home.php?erro=upload_falhou");
            exit;
        }
    } else {
        header("Location: ../pages/home.php?erro=upload_erro");
        exit;
    }
} else {
    header("Location: ../pages/home.php?erro=nenhum_arquivo");
    exit;
}
?>
