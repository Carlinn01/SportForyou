<?php
require_once "ConexaoBD.php";

class PostagemDAO {
    public static function listarTodas(): array {
        $pdo = ConexaoBD::conectar();
        $sql = "SELECT p.*, u.nome, u.nome_usuario, u.foto_perfil
                FROM postagens p
                JOIN usuarios u ON p.idusuario = u.idusuarios
                ORDER BY p.criado_em DESC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function buscarPorTexto(string $q): array {
    $pdo = ConexaoBD::conectar();
    $sql = "SELECT texto FROM postagens WHERE texto LIKE ? LIMIT 5";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["%$q%"]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
