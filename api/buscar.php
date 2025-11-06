<?php
header('Content-Type: application/json; charset=utf-8');

require_once '../login/src/ConexaoBD.php';
session_start();

$q = trim($_GET['q'] ?? '');
$filtro_esporte = trim($_GET['esporte'] ?? '');
$filtro_localizacao = trim($_GET['localizacao'] ?? '');

if ($q === '' && $filtro_esporte === '' && $filtro_localizacao === '') {
    echo json_encode([]);
    exit;
}

try {
    $pdo = ConexaoBD::conectar();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $resultados = [];

    // Buscar usuários - query simples e direta
    if ($q !== '') {
        try {
            $sqlUsuarios = "SELECT idusuarios AS id, nome, nome_usuario, foto_perfil 
                           FROM usuarios 
                           WHERE nome LIKE ? OR nome_usuario LIKE ? 
                           LIMIT 10";
            $like = "%$q%";
            $stmt = $pdo->prepare($sqlUsuarios);
            $stmt->execute([$like, $like]);
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($usuarios as $u) {
                $resultados[] = [
                    'tipo' => 'usuario',
                    'id' => $u['id'],
                    'nome' => $u['nome'] ?? '',
                    'nome_usuario' => $u['nome_usuario'] ?? '',
                    'foto_perfil' => $u['foto_perfil'] ?? 'default.png',
                    'cidade' => null,
                    'estado' => null
                ];
            }
        } catch (PDOException $e) {
            error_log("Erro ao buscar usuários: " . $e->getMessage());
        }
    }

    // Buscar eventos
    if ($q !== '') {
        try {
            $sqlEventos = "SELECT idevento AS id, titulo, tipo_esporte, local, cidade, estado, foto
                           FROM eventos
                           WHERE titulo LIKE ? OR descricao LIKE ?
                           LIMIT 10";
            $like = "%$q%";
            $stmtEventos = $pdo->prepare($sqlEventos);
            $stmtEventos->execute([$like, $like]);
            $eventos = $stmtEventos->fetchAll(PDO::FETCH_ASSOC);

            foreach ($eventos as $ev) {
                $resultados[] = [
                    'tipo' => 'evento',
                    'id' => $ev['id'],
                    'titulo' => $ev['titulo'] ?? '',
                    'tipo_esporte' => $ev['tipo_esporte'] ?? '',
                    'local' => $ev['local'] ?? '',
                    'cidade' => $ev['cidade'] ?? null,
                    'estado' => $ev['estado'] ?? null,
                    'foto' => $ev['foto'] ?? null
                ];
            }
        } catch (PDOException $e) {
            error_log("Erro ao buscar eventos: " . $e->getMessage());
        }
    }

    echo json_encode($resultados, JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    error_log("Erro na conexão: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao buscar. Tente novamente.'], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    error_log("Erro geral: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao buscar. Tente novamente.'], JSON_UNESCAPED_UNICODE);
}
?>
