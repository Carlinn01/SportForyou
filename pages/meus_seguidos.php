<?php
include("login/incs/valida-sessao.php");
require_once "login/src/ConexaoBD.php";

$conexao = ConexaoBD::conectar();
$idusuario = $_SESSION["idusuarios"]; // você

// Pega todos os usuários que te seguem
$sql = "SELECT u.idusuarios, u.nome, u.nome_usuario, u.foto_perfil
        FROM seguidores s
        JOIN usuarios u ON u.idusuarios = s.idseguidor
        WHERE s.idusuario = ?";

$stmt = $conexao->prepare($sql);
$stmt->bindParam(1, $idusuario);
$stmt->execute();

$seguidores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Meus Seguidos - SportForYou</title>
  <link rel="stylesheet" href="../assets/css/feed.css">
  <link rel="stylesheet" href="../assets/css/tema-escuro.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="../assets/js/tema.js"></script>
</head>
<body>
  <h2>Quem vc segue:</h2>

  <?php if ($seguidores): ?>
    <ul>
      <?php foreach ($seguidores as $s): ?>
        <li>
          <img src="uploads/<?= htmlspecialchars($s['foto_perfil']) ?>" width="40" height="40" style="border-radius:50%;">
          <?= htmlspecialchars($s['nome']) ?> (@<?= htmlspecialchars($s['nome_usuario']) ?>)
        </li>
      <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <p>Você Não segue ninguem ainda </p>
  <?php endif; ?>
</body>
</html>
