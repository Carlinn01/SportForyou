<?php
include("login/incs/valida-sessao.php");
require_once "login/src/SeguidoDAO.php";

$meusSeguidores = SeguidoDAO::listarSeguidores($_SESSION["idusuarios"]);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Seguidores - SportForYou</title>
    <link rel="stylesheet" href="../assets/css/feed.css">
    <link rel="stylesheet" href="../assets/css/tema-escuro.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="../assets/js/tema.js"></script>
</head>
<body>
    <h1>Seus Seguidores</h1>

    <?php if (count($meusSeguidores) > 0): ?>
        <ul>
        <?php foreach ($meusSeguidores as $seguidor): ?>
            <li>
                <img src="uploads/<?=$seguidor['foto_perfil']?>" alt="Foto de perfil" width="50" height="50">
                <?=$seguidor['nome_usuario']?>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Você ainda não tem seguidores</p>
    <?php endif; ?>
</body>
</html>
