<?php
require_once "ConexaoBD.php";

class SeguidoDAO{

//     public static function seguir($idusuario, $idseguidor) {
//     $conexao = ConexaoBD::conectar();

//     // Verifique se o relacionamento já existe
//     $sqlCheck = "SELECT COUNT(*) FROM seguidores WHERE idusuario = ? AND idseguidor = ?";
//     $stmtCheck = $conexao->prepare($sqlCheck);
//     $stmtCheck->bindParam(1, $idusuario);
//     $stmtCheck->bindParam(2, $idseguidor);
//     $stmtCheck->execute();

//     $count = $stmtCheck->fetchColumn(); // Retorna a contagem de registros encontrados

//     // Se já existir o relacionamento, avise ao usuário
//     if ($count > 0) {
//         echo "Você já está seguindo este usuário!";  // Exibe a mensagem de erro
//         return;  // Sai da função sem fazer nada
//     }

//     // Caso contrário, insira o novo relacionamento
//     $sql = "INSERT INTO seguidores (idusuario, idseguidor) VALUES (?, ?)";
//     $stmt = $conexao->prepare($sql);
//     $stmt->bindParam(1, $idusuario);
//     $stmt->bindParam(2, $idseguidor);

//     $stmt->execute();

//     echo "Agora você está seguindo este usuário!";  // Mensagem de sucesso
// }


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

    $sql = "SELECT u.nome_usuario, u.foto_perfil 
            FROM seguidores s
            JOIN usuarios u ON u.idusuarios = s.idseguidor
            WHERE s.idusuario = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(1, $idusuario);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    public static function listarSeguindo($idseguidor) {
    $conexao = ConexaoBD::conectar();

    $sql = "SELECT u.nome_usuario, u.foto_perfil
            FROM seguidores s
            JOIN usuarios u ON u.idusuarios = s.idusuario
            WHERE s.idseguidor = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(1, $idseguidor);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


}

