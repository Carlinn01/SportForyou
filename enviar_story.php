<?php
include "login/incs/valida-sessao.php"; // já garante sessão e redireciona se não estiver logado
require_once "login/src/ConexaoBD.php";

session_start();

$idusuario_logado = $_SESSION['idusuarios']; // nome correto da sessão
$pdo = ConexaoBD::conectar();

if (isset($_FILES['story'])) {
    $arquivo = $_FILES['story'];

    if ($arquivo['error'] === UPLOAD_ERR_OK) {
        $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
        $nome_arquivo = uniqid() . '.' . $extensao;
        $pasta = 'uploads/stories/';

        // Cria a pasta se não existir
        if (!is_dir($pasta)) {
            mkdir($pasta, 0777, true);
        }

        $caminho = $pasta . $nome_arquivo;

        // Move o arquivo enviado
        if (move_uploaded_file($arquivo['tmp_name'], $caminho)) {
            // Determina o tipo de mídia
            $tipo = in_array($extensao, ['mp4', 'mov', 'avi']) ? 'video' : 'imagem';

            // Insere no banco de dados
            $sql = "INSERT INTO stories (idusuario, midia, tipo) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$idusuario_logado, $caminho, $tipo]);

            header("Location: home.php");
            exit;
        } else {
            echo "Erro ao mover o arquivo enviado.";
        }
    } else {
        echo "Erro no upload do arquivo.";
    }
} else {
    echo "Nenhum arquivo foi enviado.";
}
?>
