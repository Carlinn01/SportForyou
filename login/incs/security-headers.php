<?php
/**
 * Headers de Segurança HTTP
 * Adicionar este arquivo no início de todas as páginas
 */

// Previne MIME type sniffing
header('X-Content-Type-Options: nosniff');

// Previne clickjacking
header('X-Frame-Options: DENY');

// Habilita proteção XSS do navegador
header('X-XSS-Protection: 1; mode=block');

// Política de Referrer
header('Referrer-Policy: strict-origin-when-cross-origin');

// Content Security Policy (ajustar conforme necessário)
// header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com;");

?>

