<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<!-- <body class="d-flex align-items-center d-flex justify-content-center min-vh-100"> -->
<body class="d-flex align-items-center" style="height: 100vh;">
    <form action="efetua-login.php" method="post" class="w-50 container mt-3 border rounded p-3">
        <h4>Login</h4>
        <?php
                session_start();
                if (isset($_SESSION['msg'])) {
                    echo '<div class="alert alert-danger" role="alert">';
                    echo $_SESSION['msg'];
                    unset($_SESSION['msg']);
                    echo '</div>';
                }else{
                    echo '<div class="alert alert-info" role="alert">';
                    echo 'Informe seu email e senha para entrar.';
                    echo '</div>';
                }
            ?>        
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Senha</label>
            <input type="password" name="senha" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Entrar</button>

        <div class="text-end mt-2">
            <a href="form-cadastra-usuario.html">Ainda não sou usuário</a>
        </div>
    </form>
</body>

</html>