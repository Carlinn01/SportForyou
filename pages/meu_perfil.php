<?php
include("login/incs/valida-sessao.php");
require_once "login/src/ConexaoBD.php";

$conexao = ConexaoBD::conectar();
$idusuario = $_SESSION["idusuarios"]; // ID do usuário logado

// Pega dados do usuário
$sql = "SELECT nome, nome_usuario, email, nascimento, foto_perfil FROM usuarios WHERE idusuarios = ?";
$stmt = $conexao->prepare($sql);
$stmt->bindParam(1, $idusuario);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Quantos me seguem → quem tem meu id em idseguidor
$sqlSeguidores = "SELECT COUNT(*) as total FROM seguidores WHERE idseguidor = ?";
$stmtSeg = $conexao->prepare($sqlSeguidores);
$stmtSeg->bindParam(1, $idusuario);
$stmtSeg->execute();
$totalSeguidores = $stmtSeg->fetch(PDO::FETCH_ASSOC)['total'];

// Quantos eu sigo → quem tem meu id em idusuario
$sqlSeguindo = "SELECT COUNT(*) as total FROM seguidores WHERE idusuario = ?";
$stmtSeguindo = $conexao->prepare($sqlSeguindo);
$stmtSeguindo->bindParam(1, $idusuario);
$stmtSeguindo->execute();
$totalSeguindo = $stmtSeguindo->fetch(PDO::FETCH_ASSOC)['total'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - SportForYou</title>
    <link rel="stylesheet" href="../assets/css/feed.css">
    <link rel="stylesheet" href="../assets/css/perfil.css">
    <link rel="stylesheet" href="../assets/css/tema-escuro.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="../assets/js/tema.js"></script>
</head>
<body>
    <h1>Meu Perfil</h1>

    <div class="perfil-info">
        <!-- htmlspecialchars() evita ataque xss -->
        <img src="/login/uploads/<?=$_SESSION["foto_perfil"]?>" alt="Foto de perfil" width="100" height="100">
        <div>
            <h2>Bem vindo, <?=$_SESSION["nome"]?></h2>
            <p><strong>Nome:</strong> <?= $_SESSION["nome"] ?></p>
    <p><strong>Usuário:</strong> @<?= $_SESSION["nome_usuario"] ?></p>
    <p><strong>Email:</strong> <?= $_SESSION["email"] ?></p>
    <p><strong>Data de nascimento:</strong> <?= $_SESSION["nascimento"] ?></p>
        </div>
    </div>

    <div class="stats">
        <span>Seguidores: <?= $totalSeguidores ?></span>
        <span>Seguindo: <?= $totalSeguindo ?></span>
    </div>

</body>
</html>


<!-- idseguidor (quem segue) e idusuario (quem está sendo seguido) -->
