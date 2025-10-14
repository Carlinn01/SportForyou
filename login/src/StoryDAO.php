<?php
require_once "ConexaoBD.php";

class StoryDAO {
    public static function listarRecentes(): array {
        $pdo = ConexaoBD::conectar();
        $sql = "SELECT s.*, u.nome, u.nome_usuario, u.foto_perfil
                FROM stories s
                JOIN usuarios u ON s.idusuario = u.idusuarios
                WHERE s.criado_em >= NOW() - INTERVAL 24 HOUR
                ORDER BY s.criado_em DESC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function criar(int $idusuario, string $midia, string $tipo = 'imagem'): bool {
        $pdo = ConexaoBD::conectar();
        $sql = "INSERT INTO stories (idusuario, midia, tipo) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$idusuario, $midia, $tipo]);
    }
}
