<?php
require_once "login/src/PostagemDAO.php";
require_once "login/src/UsuarioDAO.php";

header('Content-Type: application/json');

$q = $_GET['q'] ?? '';
$q = trim($q);

$results = [];

// Buscar usuários
if(!empty($q)){
    $usuarios = UsuarioDAO::buscarPorNomeOuUsuario($q);
    foreach($usuarios as $user){
        $results[] = [
            'tipo' => 'usuario',
            'nome' => $user['nome'],
            'nome_usuario' => $user['nome_usuario']
        ];
    }

    // Buscar postagens
    $postagens = PostagemDAO::buscarPorTexto($q);
    foreach($postagens as $post){
        $results[] = [
            'tipo' => 'postagem',
            'texto' => $post['texto']
        ];
    }
}

echo json_encode($results);
