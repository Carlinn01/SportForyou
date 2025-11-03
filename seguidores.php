<?php
    include "login/incs/valida-sessao.php";
    require_once "login/src/UsuarioDAO.php";
    require_once "login/src/SeguidoDAO.php";



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
        <a href="home.php">Ir para o Feed</a>
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
    // Verificar se o usuário atual já está seguindo a pessoa
    // A função estaSeguindo verifica: WHERE idusuario = ? AND idseguidor = ?
    // Mas segundo a estrutura: idusuario = quem é seguido, idseguidor = quem está seguindo
    // Então precisamos inverter os parâmetros: estaSeguindo(quem_é_seguido, quem_está_seguindo)
    $jaSeguindo = SeguidoDAO::estaSeguindo($usuarios[$i]['idusuarios'], $_SESSION['idusuarios']);
?>
    <div class="usuario">
        <span><?= $usuarios[$i]["nome"] ?></span>
        <div>
            <?php if ($jaSeguindo): ?>
                <!-- Se já estiver seguindo, mostrar opção de deixar de seguir -->
                <a href="deixar_seguir.php?idseguidor=<?= $usuarios[$i]['idusuarios'] ?>">Deixar de seguir</a>
            <?php else: ?>
                <!-- Caso contrário, mostrar opção de seguir -->
                <a href="seguir.php?idseguidor=<?= $usuarios[$i]['idusuarios'] ?>">Seguir</a>
            <?php endif; ?>
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