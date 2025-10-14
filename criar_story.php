<?php
session_start();
require_once "login/src/ConexaoBD.php";

$pdo = ConexaoBD::conectar();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idusuario = $_SESSION['idusuarios'];

    if (!empty($_FILES['story']['name'])) {
        $diretorio = "login/uploads/stories/";
        if (!is_dir($diretorio)) {
            mkdir($diretorio, 0777, true);
        }

        $nome_arquivo = time() . "_" . basename($_FILES['story']['name']);
        $caminho = $diretorio . $nome_arquivo;

        if (move_uploaded_file($_FILES['story']['tmp_name'], $caminho)) {
            $sql = "INSERT INTO stories (idusuario, imagem) VALUES (?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$idusuario, $nome_arquivo]);

            header("Location: home.php");
            exit;
        } else {
            echo "Erro ao enviar o story.";
        }
    } else {
        echo "Nenhum arquivo enviado.";
    }
}
?>
