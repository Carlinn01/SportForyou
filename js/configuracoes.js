// JavaScript para página de configurações

document.addEventListener('DOMContentLoaded', function() {
    // Sistema de Tema (Claro/Escuro)
    const temaClaro = document.getElementById('tema-claro');
    const temaEscuro = document.getElementById('tema-escuro');
    
    // Carrega tema salvo
    const temaSalvo = localStorage.getItem('tema') || 'claro';
    
    if (temaSalvo === 'escuro') {
        temaEscuro.checked = true;
    } else {
        temaClaro.checked = true;
    }
    
    // Event listeners para mudança de tema
    if (temaClaro) {
        temaClaro.addEventListener('change', function() {
            if (this.checked) {
                mudarTema('claro');
            }
        });
    }
    
    if (temaEscuro) {
        temaEscuro.addEventListener('change', function() {
            if (this.checked) {
                mudarTema('escuro');
            }
        });
    }
    
    // Validação do formulário de senha
    const formSenha = document.getElementById('form-senha');
    if (formSenha) {
        formSenha.addEventListener('submit', function(e) {
            const novaSenha = document.getElementById('nova_senha').value;
            const confirmarSenha = document.getElementById('confirmar_senha').value;
            
            if (novaSenha !== confirmarSenha) {
                e.preventDefault();
                alert('As senhas não coincidem!');
                return false;
            }
            
            if (novaSenha.length < 6) {
                e.preventDefault();
                alert('A senha deve ter pelo menos 6 caracteres!');
                return false;
            }
        });
    }
});

