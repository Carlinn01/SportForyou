// Sistema de Tema Global para toda a aplicação

document.addEventListener('DOMContentLoaded', function() {
    // Carrega tema salvo do localStorage
    const temaSalvo = localStorage.getItem('tema') || 'claro';
    
    // Aplica tema ao carregar a página
    if (temaSalvo === 'escuro') {
        aplicarTemaEscuro();
    } else {
        aplicarTemaClaro();
    }
});

function aplicarTemaClaro() {
    document.body.classList.remove('tema-escuro');
    document.body.classList.add('tema-claro');
    
    // Aplica cores do tema claro
    document.documentElement.style.setProperty('--bg-primary', '#ffffff');
    document.documentElement.style.setProperty('--bg-secondary', '#f5f5f5');
    document.documentElement.style.setProperty('--bg-sidebar', '#ffffff');
    document.documentElement.style.setProperty('--text-primary', '#333333');
    document.documentElement.style.setProperty('--text-secondary', '#666666');
    document.documentElement.style.setProperty('--border-color', '#ddd');
}

function aplicarTemaEscuro() {
    document.body.classList.remove('tema-claro');
    document.body.classList.add('tema-escuro');
    
    // Aplica cores do tema escuro
    document.documentElement.style.setProperty('--bg-primary', '#1a1a1a');
    document.documentElement.style.setProperty('--bg-secondary', '#2d2d2d');
    document.documentElement.style.setProperty('--bg-sidebar', '#2d2d2d');
    document.documentElement.style.setProperty('--text-primary', '#ffffff');
    document.documentElement.style.setProperty('--text-secondary', '#cccccc');
    document.documentElement.style.setProperty('--border-color', '#444');
}

// Função para mudar tema (chamada da página de configurações)
function mudarTema(tema) {
    if (tema === 'escuro') {
        localStorage.setItem('tema', 'escuro');
        aplicarTemaEscuro();
    } else {
        localStorage.setItem('tema', 'claro');
        aplicarTemaClaro();
    }
}

