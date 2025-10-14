<?php
require_once 'login/src/ConexaoBD.php';
session_start();

header('Content-Type: application/json');

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

// Buscar postagens
$sqlPosts = "SELECT 
                CASE 
                    WHEN COLUMN_NAME = 'idPostagem' THEN idPostagem
                    ELSE idpostagem 
                END AS id,
                texto
             FROM postagens 
             WHERE texto LIKE :q 
             LIMIT 5";

try {
    $stmt = $pdo->prepare("SELECT idPostagem AS id, texto FROM postagens WHERE texto LIKE :q LIMIT 5");
    $stmt->execute([':q' => "%$q%"]);
    $postagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Se não encontrou com idPostagem, tenta com idpostagem
    if (empty($postagens)) {
        $stmt = $pdo->prepare("SELECT idpostagem AS id, texto FROM postagens WHERE texto LIKE :q LIMIT 5");
        $stmt->execute([':q' => "%$q%"]);
        $postagens = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    foreach ($postagens as $p) {
        $resultados[] = [
            'tipo' => 'postagem',
            'id' => $p['id'],
            'texto' => $p['texto']
        ];
    }
} catch (Exception $e) {
    error_log("Erro ao buscar postagens: " . $e->getMessage());
}


echo json_encode($resultados);
?>
