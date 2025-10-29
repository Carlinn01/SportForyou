<?php
require_once 'login/src/ConexaoBD.php';
session_start();


$q = trim($_GET['q'] ?? '');

if ($q === '') {
    echo json_encode([]);
    exit;
}

$pdo = ConexaoBD::conectar();
$resultados = [];

// Buscar usuários
$sqlUsuarios = "SELECT idusuarios AS id, nome, nome_usuario, foto_perfil 
                FROM usuarios 
                WHERE nome LIKE :q OR nome_usuario LIKE :q 
                LIMIT 5";
$stmt = $pdo->prepare($sqlUsuarios);
$stmt->execute([':q' => "%$q%"]);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($usuarios as $u) {
    $resultados[] = [
        'tipo' => 'usuario',
        'id' => $u['id'],
        'nome' => $u['nome'],
        'nome_usuario' => $u['nome_usuario'],
        'foto_perfil' => $u['foto_perfil']
    ];
}




echo json_encode($resultados);
?>
