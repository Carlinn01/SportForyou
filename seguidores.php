<?php
    include "login/incs/valida-sessao.php";
    require_once "login/src/UsuarioDAO.php";



    if (!isset($_GET["nome"])) {
        $_GET["nome"] = "";
    }

    $usuarios = UsuarioDAO::buscarUsuarioNome($_GET["nome"],$_SESSION['idusuarios']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicione seguidores</title>
    <link rel="stylesheet" href="/login/seguidores.css">
</head>
<body>
    <header>

    </header>
    <main>
        <h1>Adicione Seguidores!</h1>

        <form action="">
        <div class="busca">
        <label for="">Nome de Usuário:</label>
        <input type="text" name="nome" placeholder="Nome de Usuário"> 
        <button type="submit">Buscar</button>
        </div>
        </form>

        <h3>Lista de Usuários (FIltrado por nome)</h3>
<?php
for ($i = 0; $i < count($usuarios); $i++) {
?>
    <div class="usuario">
        <span><?= $usuarios[$i]["nome"] ?></span>
        <div>
            <a href="seguir.php?idseguidor=<?= $usuarios[$i]['idusuarios'] ?>">Adicionar</a>
            <a href="deixar_seguir.php?idseguidor=<?= $usuarios[$i]['idusuarios'] ?>">Deixar de seguir</a>
        </div>
    </div>
<?php 
}
?>

        </div>

    </main>
    <footer>

    </footer>
</body>
</html>