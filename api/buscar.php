<?php
require_once '../login/src/ConexaoBD.php';
session_start();

$q = trim($_GET['q'] ?? '');
$filtro_esporte = trim($_GET['esporte'] ?? '');
$filtro_localizacao = trim($_GET['localizacao'] ?? '');

if ($q === '' && $filtro_esporte === '' && $filtro_localizacao === '') {
    echo json_encode([]);
    exit;
}

$pdo = ConexaoBD::conectar();
$resultados = [];

// Buscar usuários com filtros
$sqlUsuarios = "SELECT u.idusuarios AS id, u.nome, u.nome_usuario, u.foto_perfil, u.esportes_favoritos, e.cidade, e.estado
                FROM usuarios u
                LEFT JOIN eventos e ON e.organizador_id = u.idusuarios
                WHERE 1=1";
$params = [];

if ($q !== '') {
    $sqlUsuarios .= " AND (u.nome LIKE :q OR u.nome_usuario LIKE :q)";
    $params[':q'] = "%$q%";
}

if ($filtro_esporte !== '') {
    $sqlUsuarios .= " AND (u.esportes_favoritos LIKE :esporte)";
    $params[':esporte'] = "%$filtro_esporte%";
}

if ($filtro_localizacao !== '') {
    $sqlUsuarios .= " AND (e.cidade LIKE :local OR e.estado LIKE :local OR u.esportes_favoritos LIKE :local)";
    $params[':local'] = "%$filtro_localizacao%";
}

$sqlUsuarios .= " GROUP BY u.idusuarios LIMIT 10";
$stmt = $pdo->prepare($sqlUsuarios);
$stmt->execute($params);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($usuarios as $u) {
    $resultados[] = [
        'tipo' => 'usuario',
        'id' => $u['id'],
        'nome' => $u['nome'],
        'nome_usuario' => $u['nome_usuario'],
        'foto_perfil' => $u['foto_perfil'],
        'cidade' => $u['cidade'] ?? null,
        'estado' => $u['estado'] ?? null
    ];
}

// Buscar eventos
$sqlEventos = "SELECT e.idevento AS id, e.titulo, e.tipo_esporte, e.local, e.cidade, e.estado, e.foto
               FROM eventos e
               WHERE 1=1";
$paramsEventos = [];

if ($q !== '') {
    $sqlEventos .= " AND (e.titulo LIKE :q_evento OR e.descricao LIKE :q_evento)";
    $paramsEventos[':q_evento'] = "%$q%";
}

if ($filtro_esporte !== '') {
    $sqlEventos .= " AND e.tipo_esporte LIKE :esporte_evento";
    $paramsEventos[':esporte_evento'] = "%$filtro_esporte%";
}

if ($filtro_localizacao !== '') {
    $sqlEventos .= " AND (e.cidade LIKE :local_evento OR e.estado LIKE :local_evento OR e.local LIKE :local_evento)";
    $paramsEventos[':local_evento'] = "%$filtro_localizacao%";
}

$sqlEventos .= " LIMIT 10";
$stmtEventos = $pdo->prepare($sqlEventos);
$stmtEventos->execute($paramsEventos);
$eventos = $stmtEventos->fetchAll(PDO::FETCH_ASSOC);

foreach ($eventos as $ev) {
    $resultados[] = [
        'tipo' => 'evento',
        'id' => $ev['id'],
        'titulo' => $ev['titulo'],
        'tipo_esporte' => $ev['tipo_esporte'],
        'local' => $ev['local'],
        'cidade' => $ev['cidade'],
        'estado' => $ev['estado'],
        'foto' => $ev['foto']
    ];
}

echo json_encode($resultados);
?>
