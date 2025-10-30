<?php

require_once "ConexaoBD.php";

class ComentarioDAO {

    public static function listarComentarios($idpostagem): array {
        $pdo = ConexaoBD::conectar();
        $sql = "SELECT c.*, u.nome_usuario
                FROM comentarios c
                JOIN usuarios u ON c.idusuario = u.idusuarios
                WHERE c.idpostagem = ?
                ORDER BY c.data_comentario DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idpostagem]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
