<?php
require_once "ConexaoBD.php";

class SeguidoDAO{

    public static function seguir($idusuario, $idseguidor) {
        $conexao = ConexaoBD::conectar();

        $sql = "insert into seguidores (idusuario, idseguidor) values (?,?)";
        $stmt = $conexao->prepare($sql);

        $stmt->bindParam(1, $idusuario);
        $stmt->bindParam(2, $idseguidor);

        $stmt->execute();

    }

    public static function deixarDeSeguir($idusuario, $idseguidor) {
    $conexao = ConexaoBD::conectar();

    $sql = "DELETE FROM seguidores WHERE idusuario = ? AND idseguidor = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(1, $idusuario);
    $stmt->bindParam(2, $idseguidor);
    $stmt->execute();
}

    public static function listarSeguidores($idusuario) {
    $conexao = ConexaoBD::conectar();

    $sql = "SELECT u.idusuarios, u.nome, u.nome_usuario, u.foto_perfil 
            FROM seguidores s
            JOIN usuarios u ON u.idusuarios = s.idseguidor
            WHERE s.idusuario = ?
            ORDER BY u.nome ASC";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(1, $idusuario);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    public static function listarSeguindo($idseguidor) {
    $conexao = ConexaoBD::conectar();

    $sql = "SELECT u.idusuarios, u.nome, u.nome_usuario, u.foto_perfil
            FROM seguidores s
            JOIN usuarios u ON u.idusuarios = s.idusuario
            WHERE s.idseguidor = ?
            ORDER BY u.nome ASC";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(1, $idseguidor);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public static function estaSeguindo($idusuario, $idseguidor) {
    $conexao = ConexaoBD::conectar();

    // Verificar se já existe um registro de seguimento entre os dois
    $sql = "SELECT COUNT(*) FROM seguidores WHERE idusuario = ? AND idseguidor = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(1, $idusuario);
    $stmt->bindParam(2, $idseguidor);
    $stmt->execute();

    // Retorna verdadeiro (1) ou falso (0)
    return $stmt->fetchColumn() > 0;
}

}

