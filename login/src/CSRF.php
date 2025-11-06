<?php
/**
 * Classe para gerenciar tokens CSRF
 */
class CSRF {
    
    /**
     * Gera um token CSRF e armazena na sessão
     * @return string Token CSRF
     */
    public static function gerarToken(): string {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Valida um token CSRF
     * @param string $token Token a ser validado
     * @return bool True se válido, False caso contrário
     */
    public static function validarToken(string $token): bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Gera um novo token (regenera)
     */
    public static function regenerarToken(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    /**
     * Retorna o campo hidden HTML para formulários
     * @return string HTML do campo hidden
     */
    public static function campoHidden(): string {
        $token = self::gerarToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }
}

