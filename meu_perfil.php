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
    <title>Meu Perfil</title>
</head>
<body>
    <h1>Meu Perfil</h1>

    <div class="perfil-info">
        <!-- htmlspecialchars() evita ataque xss -->
        <img src="uploads/<?= htmlspecialchars($usuario['foto_perfil']) ?>" alt="Foto de perfil" width="100" height="100">
        <div>
            <p><strong>Nome:</strong> <?= htmlspecialchars($usuario['nome']) ?></p>
            <p><strong>Usuário:</strong> @<?= htmlspecialchars($usuario['nome_usuario']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($usuario['email']) ?></p>
            <p><strong>Data de nascimento:</strong> <?= htmlspecialchars($usuario['nascimento']) ?></p>
        </div>
    </div>

    <div class="stats">
        <span>Seguidores: <?= $totalSeguidores ?></span>
        <span>Seguindo: <?= $totalSeguindo ?></span>
    </div>

</body>
</html>


<!-- idseguidor (quem segue) e idusuario (quem está sendo seguido) -->
