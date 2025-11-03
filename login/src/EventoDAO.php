<?php
require_once "ConexaoBD.php";

class EventoDAO {
    
    // Lista todos os eventos ordenados por data
    public static function listarTodos(): array {
        $pdo = ConexaoBD::conectar();
        $sql = "SELECT e.*, u.nome as organizador_nome, u.nome_usuario as organizador_usuario, u.foto_perfil,
                (SELECT COUNT(*) FROM eventos_interessados WHERE evento_id = e.idevento) AS total_interessados
                FROM eventos e
                JOIN usuarios u ON e.organizador_id = u.idusuarios
                ORDER BY e.data_evento ASC, e.hora_evento ASC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Lista eventos por tipo de esporte
    public static function listarPorEsporte(string $tipo_esporte): array {
        $pdo = ConexaoBD::conectar();
        $sql = "SELECT e.*, u.nome as organizador_nome, u.nome_usuario as organizador_usuario, u.foto_perfil,
                (SELECT COUNT(*) FROM eventos_interessados WHERE evento_id = e.idevento) AS total_interessados
                FROM eventos e
                JOIN usuarios u ON e.organizador_id = u.idusuarios
                WHERE e.tipo_esporte = ?
                ORDER BY e.data_evento ASC, e.hora_evento ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$tipo_esporte]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Busca evento por ID
    public static function buscarPorId(int $idevento): ?array {
        $pdo = ConexaoBD::conectar();
        $sql = "SELECT e.*, u.nome as organizador_nome, u.nome_usuario as organizador_usuario, u.foto_perfil,
                (SELECT COUNT(*) FROM eventos_interessados WHERE evento_id = e.idevento) AS total_interessados
                FROM eventos e
                JOIN usuarios u ON e.organizador_id = u.idusuarios
                WHERE e.idevento = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idevento]);
        $evento = $stmt->fetch(PDO::FETCH_ASSOC);
        return $evento ?: null;
    }
    
    // Cria novo evento
    public static function criar(array $dados): int {
        $pdo = ConexaoBD::conectar();
        $sql = "INSERT INTO eventos (organizador_id, titulo, descricao, tipo_esporte, data_evento, hora_evento, local, cidade, estado, foto) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $dados['organizador_id'],
            $dados['titulo'],
            $dados['descricao'] ?? null,
            $dados['tipo_esporte'],
            $dados['data_evento'],
            $dados['hora_evento'] ?? null,
            $dados['local'],
            $dados['cidade'] ?? null,
            $dados['estado'] ?? null,
            $dados['foto'] ?? null
        ]);
        return $pdo->lastInsertId();
    }
    
    // Atualiza evento
    public static function atualizar(int $idevento, array $dados): bool {
        $pdo = ConexaoBD::conectar();
        $sql = "UPDATE eventos SET titulo = ?, descricao = ?, tipo_esporte = ?, data_evento = ?, hora_evento = ?, 
                local = ?, cidade = ?, estado = ?, foto = ?
                WHERE idevento = ? AND organizador_id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            $dados['titulo'],
            $dados['descricao'] ?? null,
            $dados['tipo_esporte'],
            $dados['data_evento'],
            $dados['hora_evento'] ?? null,
            $dados['local'],
            $dados['cidade'] ?? null,
            $dados['estado'] ?? null,
            $dados['foto'] ?? null,
            $idevento,
            $dados['organizador_id']
        ]);
    }
    
    // Deleta evento
    public static function deletar(int $idevento, int $organizador_id): bool {
        $pdo = ConexaoBD::conectar();
        $sql = "DELETE FROM eventos WHERE idevento = ? AND organizador_id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$idevento, $organizador_id]);
    }
    
    // Verifica se usuário tem interesse no evento
    public static function temInteresse(int $evento_id, int $usuario_id): bool {
        $pdo = ConexaoBD::conectar();
        $sql = "SELECT COUNT(*) FROM eventos_interessados WHERE evento_id = ? AND usuario_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$evento_id, $usuario_id]);
        return $stmt->fetchColumn() > 0;
    }
    
    // Adiciona interesse no evento
    public static function adicionarInteresse(int $evento_id, int $usuario_id): bool {
        $pdo = ConexaoBD::conectar();
        try {
            $sql = "INSERT INTO eventos_interessados (evento_id, usuario_id) VALUES (?, ?)";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([$evento_id, $usuario_id]);
        } catch (PDOException $e) {
            // Se já existe interesse, retorna false
            return false;
        }
    }
    
    // Remove interesse no evento
    public static function removerInteresse(int $evento_id, int $usuario_id): bool {
        $pdo = ConexaoBD::conectar();
        $sql = "DELETE FROM eventos_interessados WHERE evento_id = ? AND usuario_id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$evento_id, $usuario_id]);
    }
    
    // Lista eventos do organizador
    public static function listarPorOrganizador(int $organizador_id): array {
        $pdo = ConexaoBD::conectar();
        $sql = "SELECT e.*, 
                (SELECT COUNT(*) FROM eventos_interessados WHERE evento_id = e.idevento) AS total_interessados
                FROM eventos e
                WHERE e.organizador_id = ?
                ORDER BY e.data_evento DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$organizador_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Lista eventos que o usuário tem interesse e que ainda não aconteceram
    public static function listarEventosInteresse(int $usuario_id): array {
        $pdo = ConexaoBD::conectar();
        $hoje = date('Y-m-d');
        $sql = "SELECT e.*, u.nome as organizador_nome, u.nome_usuario as organizador_usuario, u.foto_perfil,
                (SELECT COUNT(*) FROM eventos_interessados WHERE evento_id = e.idevento) AS total_interessados
                FROM eventos e
                JOIN usuarios u ON e.organizador_id = u.idusuarios
                INNER JOIN eventos_interessados ei ON e.idevento = ei.evento_id
                WHERE ei.usuario_id = ? AND e.data_evento >= ?
                ORDER BY e.data_evento ASC, e.hora_evento ASC
                LIMIT 5";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$usuario_id, $hoje]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

