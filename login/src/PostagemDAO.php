<?php
require_once "ConexaoBD.php";

class PostagemDAO {
    // Listar todas as postagens com informações de curtidas e comentários
    public static function listarTodas(): array {
        $pdo = ConexaoBD::conectar();
        
        // Consulta SQL para pegar postagens com informações de curtidas
          $sql = "SELECT p.*, u.nome, u.nome_usuario, u.foto_perfil,
            (SELECT COUNT(*) FROM curtidas WHERE idpostagem = p.idpostagem) AS curtidas
            FROM postagens p
            JOIN usuarios u ON p.idusuario = u.idusuarios
            ORDER BY p.criado_em DESC";
        
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Buscar postagens por texto (como você já tem)
    public static function buscarPorTexto(string $q): array {
        $pdo = ConexaoBD::conectar();
        $sql = "SELECT texto FROM postagens WHERE texto LIKE ? LIMIT 5";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["%$q%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Contar as curtidas de uma postagem
    public static function contarCurtidas(int $idpostagem): int {
        $pdo = ConexaoBD::conectar();
        $sql = "SELECT COUNT(*) FROM curtidas WHERE idpostagem = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idpostagem]);
        return $stmt->fetchColumn();
    }

    // Contar os comentários de uma postagem
    public static function contarComentarios(int $idpostagem): int {
        $pdo = ConexaoBD::conectar();
        $sql = "SELECT COUNT(*) FROM comentarios WHERE idpostagem = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idpostagem]);
        return $stmt->fetchColumn();
    }

    // Adicionar um comentário
    public static function adicionarComentario(int $idusuario, int $idpostagem, string $texto): void {
        $pdo = ConexaoBD::conectar();
        $sql = "INSERT INTO comentarios (idusuario, idpostagem, texto) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idusuario, $idpostagem, $texto]);
    }

    // Adicionar uma curtida
    public static function adicionarCurtida(int $idusuario, int $idpostagem): void {
        $pdo = ConexaoBD::conectar();
        $sql = "INSERT INTO curtidas (idusuario, idpostagem) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idusuario, $idpostagem]);
    }

    // Remover uma curtida
    public static function removerCurtida(int $idusuario, int $idpostagem): void {
        $pdo = ConexaoBD::conectar();
        $sql = "DELETE FROM curtidas WHERE idusuario = ? AND idpostagem = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idusuario, $idpostagem]);
    }

    // Verificar se o usuário já curtiu uma postagem
    public static function usuarioCurtiu(int $idusuario, int $idpostagem): bool {
        $pdo = ConexaoBD::conectar();
        $sql = "SELECT 1 FROM curtidas WHERE idusuario = ? AND idpostagem = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idusuario, $idpostagem]);
        return $stmt->fetchColumn() ? true : false;
    }
}
