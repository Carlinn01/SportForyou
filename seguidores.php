<?php
    include "login/incs/valida-sessao.php";
    require_once "login/src/UsuarioDAO.php";

    $usuarios = UsuarioDAO::Listar($_SESSION['idusuarios']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicione seguidores</title>
</head>
<body>
    <header>

    </header>
    <main>
        <h1>Adicione Seguidores!</h1>

        <label for="">Nome de Usuário:</label>
        <input type="text" placeholder="Nome de Usuário"> 
        <button>Buscar</button>

        <h3>Lista de Usuarios</h3>
        <?php
        for ($i=0; $i < count($usuarios) ; $i++) {
            ?>
            <p><?=$usuarios[$i]["nome_usuario"]?> <a href="seguir.php?idseguido=<?=$usuarios[$i]['idusuarios']?>">Adicionar</a></p>
            <?php 
               }
        ?>

    </main>
    <footer>

    </footer>
</body>
</html>