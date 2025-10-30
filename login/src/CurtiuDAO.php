<?php
require_once "ConexaoBD.php";

class CurtiuDAO {
    // Verifica se o usuário já curtiu a postagem
    public static function verificarCurtiu(int $idusuario, int $idpostagem): bool {
        $pdo = ConexaoBD::conectar();
        $sql = "SELECT * FROM curtidas WHERE idusuario = ? AND idpostagem = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idusuario, $idpostagem]);
        return $stmt->rowCount() > 0;
    }

    // Adiciona uma curtida
    public static function adicionarCurtiu(int $idusuario, int $idpostagem): void {
        $pdo = ConexaoBD::conectar();
        $sql = "INSERT INTO curtidas (idusuario, idpostagem) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idusuario, $idpostagem]);
    }

    // Remove uma curtida
    public static function removerCurtiu(int $idusuario, int $idpostagem): void {
        $pdo = ConexaoBD::conectar();
        $sql = "DELETE FROM curtidas WHERE idusuario = ? AND idpostagem = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idusuario, $idpostagem]);
    }

    // Conta o número de curtidas para uma postagem
    public static function contarCurtidas(int $idpostagem): int {
        $pdo = ConexaoBD::conectar();
        $sql = "SELECT COUNT(*) FROM curtidas WHERE idpostagem = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idpostagem]);
        return (int)$stmt->fetchColumn();
    }
}
?>
