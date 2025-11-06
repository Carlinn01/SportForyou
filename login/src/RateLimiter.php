<?php
/**
 * Classe para Rate Limiting
 */
class RateLimiter {
    
    private static $diretorio = '../login/tmp/rate_limit/';
    
    /**
     * Verifica se o IP/usuário excedeu o limite
     * @param string $chave Chave única (IP ou ID do usuário)
     * @param int $limite Número máximo de requisições
     * @param int $janela Janela de tempo em segundos
     * @return bool True se permitido, False se bloqueado
     */
    public static function verificar(string $chave, int $limite = 10, int $janela = 60): bool {
        if (!is_dir(self::$diretorio)) {
            mkdir(self::$diretorio, 0755, true);
        }
        
        $arquivo = self::$diretorio . md5($chave) . '.json';
        $agora = time();
        
        if (file_exists($arquivo)) {
            $dados = json_decode(file_get_contents($arquivo), true);
            
            // Remove requisições antigas (fora da janela)
            $dados['requisicoes'] = array_filter($dados['requisicoes'], function($timestamp) use ($agora, $janela) {
                return ($agora - $timestamp) < $janela;
            });
            
            // Reindexa array
            $dados['requisicoes'] = array_values($dados['requisicoes']);
            
            // Verifica se excedeu o limite
            if (count($dados['requisicoes']) >= $limite) {
                return false;
            }
        } else {
            $dados = ['requisicoes' => []];
        }
        
        // Adiciona nova requisição
        $dados['requisicoes'][] = $agora;
        file_put_contents($arquivo, json_encode($dados));
        
        return true;
    }
    
    /**
     * Limpa requisições antigas
     */
    public static function limpar(): void {
        if (!is_dir(self::$diretorio)) {
            return;
        }
        
        $arquivos = glob(self::$diretorio . '*.json');
        $agora = time();
        
        foreach ($arquivos as $arquivo) {
            $dados = json_decode(file_get_contents($arquivo), true);
            if (!$dados) {
                unlink($arquivo);
                continue;
            }
            
            // Remove requisições com mais de 1 hora
            $dados['requisicoes'] = array_filter($dados['requisicoes'], function($timestamp) use ($agora) {
                return ($agora - $timestamp) < 3600;
            });
            
            if (empty($dados['requisicoes'])) {
                unlink($arquivo);
            } else {
                $dados['requisicoes'] = array_values($dados['requisicoes']);
                file_put_contents($arquivo, json_encode($dados));
            }
        }
    }
    
    /**
     * Obtém IP do cliente
     * @return string IP do cliente
     */
    public static function obterIP(): string {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_X_REAL_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        
        // Se for lista de IPs (proxy), pega o primeiro
        if (strpos($ip, ',') !== false) {
            $ip = trim(explode(',', $ip)[0]);
        }
        
        return $ip;
    }
}

