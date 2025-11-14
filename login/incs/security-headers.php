<?php
/**
 * Headers de Segurança HTTP
 * Adicionar este arquivo no início de todas as páginas PHP
 * 
 * IMPORTANTE: Chame esta função antes de qualquer output HTML
 */

if (!function_exists('setSecurityHeaders')) {
    function setSecurityHeaders() {
        // Previne MIME type sniffing
        header('X-Content-Type-Options: nosniff');
        
        // Previne clickjacking (permite mesmo domínio, bloqueia outros)
        header('X-Frame-Options: SAMEORIGIN');
        
        // Habilita proteção XSS do navegador
        header('X-XSS-Protection: 1; mode=block');
        
        // Política de Referrer
        header('Referrer-Policy: strict-origin-when-cross-origin');
        
        // Permissões de recursos (restringe acesso a geolocalização, câmera, etc)
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
        
        // Content Security Policy (ajustado para permitir recursos externos necessários)
        $csp = "default-src 'self'; ";
        $csp .= "script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com; ";
        $csp .= "style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com https://fonts.gstatic.com; ";
        $csp .= "img-src 'self' data: https: blob:; ";
        $csp .= "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com data:; ";
        $csp .= "connect-src 'self'; ";
        $csp .= "frame-ancestors 'self';";
        header("Content-Security-Policy: $csp");
        
        // Remove informações do servidor (se possível)
        if (function_exists('header_remove')) {
            @header_remove('Server');
            @header_remove('X-Powered-By');
        }
        
        // Strict Transport Security (HSTS) - apenas se estiver usando HTTPS
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
        }
        
        // Previne cache de páginas sensíveis (login, admin, etc)
        $sensitivePages = ['login.php', 'efetua-login.php', 'admin', 'configuracoes'];
        $currentPage = basename($_SERVER['PHP_SELF']);
        
        foreach ($sensitivePages as $page) {
            if (strpos($currentPage, $page) !== false || strpos($_SERVER['REQUEST_URI'], $page) !== false) {
                header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
                header('Pragma: no-cache');
                header('Expires: 0');
                break;
            }
        }
    }
}

// Chama a função automaticamente
setSecurityHeaders();

?>

