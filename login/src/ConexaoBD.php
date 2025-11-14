<?php
class ConexaoBD{

    public static function conectar():PDO{
        // Configura timezone para América/São_Paulo (horário do servidor)
        date_default_timezone_set('America/Sao_Paulo');
        
        // Credenciais do banco de dados InfinityFree
        $host = 'sql111.infinityfree.com';
        $port = '3306';
        $dbname = 'if0_40354463_meuprojeto';
        $username = 'if0_40354463';
        $password = 'Cd6i57jxyXa3DB';
        
        $conexao = new PDO("mysql:host=$host:$port;dbname=$dbname", $username, $password);
        $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Configura timezone no MySQL também
        $conexao->exec("SET time_zone = '-03:00'");
        return $conexao;
    }
}
