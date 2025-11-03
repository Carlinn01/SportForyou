<?php

require_once "ConexaoBD.php";

class ComentarioDAO {

    public static function listarComentarios($idpostagem): array {
        $pdo = ConexaoBD::conectar();
         $sql = "SELECT c.*, u.nome_usuario, u.foto_perfil FROM comentarios c
                JOIN usuarios u ON c.idusuario = u.idusuarios
                WHERE c.idpostagem = ? ORDER BY c.data_comentario DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idpostagem]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método otimizado para carregar comentários de múltiplas postagens de uma vez (evita N+1)
    public static function listarComentariosPorPostagens(array $idsPostagens): array {
        if (empty($idsPostagens)) {
            return [];
        }

        $pdo = ConexaoBD::conectar();
        
        // Cria placeholders para o IN clause
        $placeholders = implode(',', array_fill(0, count($idsPostagens), '?'));
        
        $sql = "SELECT c.*, u.nome_usuario, u.foto_perfil 
                FROM comentarios c
                JOIN usuarios u ON c.idusuario = u.idusuarios
                WHERE c.idpostagem IN ($placeholders) 
                ORDER BY c.data_comentario DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($idsPostagens);
        
        // Agrupa comentários por idpostagem
        $comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resultado = [];
        
        foreach ($comentarios as $comentario) {
            $idpostagem = $comentario['idpostagem'];
            if (!isset($resultado[$idpostagem])) {
                $resultado[$idpostagem] = [];
            }
            $resultado[$idpostagem][] = $comentario;
        }
        
        return $resultado;
    }
}
