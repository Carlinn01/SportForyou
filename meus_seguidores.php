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
    <title>Meus Seguidores</title>
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
