// Sistema de mensagens em tempo real

let intervalId = null;
let ultimaMensagemId = 0;

// Inicializa quando a página carrega
document.addEventListener('DOMContentLoaded', function() {
    const mensagensArea = document.getElementById('mensagens-area');
    if (mensagensArea) {
        const conversaId = mensagensArea.dataset.conversa;
        
        // Pega o ID da última mensagem visível
        const ultimasMensagens = mensagensArea.querySelectorAll('.mensagem-item');
        if (ultimasMensagens.length > 0) {
            // Pega o ID da última mensagem do dataset ou do último elemento
            const ultimoElemento = ultimasMensagens[ultimasMensagens.length - 1];
            ultimaMensagemId = parseInt(ultimoElemento.dataset.mensagemId || ultimoElemento.getAttribute('data-mensagem-id') || '0');
        }
        
        // Inicia polling para novas mensagens
        if (conversaId) {
            intervalId = setInterval(function() {
                buscarNovasMensagens(conversaId);
            }, 3000); // Verifica a cada 3 segundos
        }
        
        // Scroll para o final das mensagens
        scrollToBottom();
    }
    
    // Formulário de enviar mensagem
    const formMensagem = document.getElementById('form-enviar-mensagem');
    if (formMensagem) {
        formMensagem.addEventListener('submit', function(e) {
            e.preventDefault();
            enviarMensagem();
        });
    }
    
    // Enter para enviar mensagem
    const inputMensagem = document.getElementById('input-mensagem');
    if (inputMensagem) {
        inputMensagem.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                enviarMensagem();
            }
        });
    }
});

// Função para buscar novas mensagens
function buscarNovasMensagens(conversaId) {
    fetch(`buscar_mensagens.php?conversa_id=${conversaId}&ultima_mensagem_id=${ultimaMensagemId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.mensagens.length > 0) {
                const mensagensArea = document.getElementById('mensagens-area');
                data.mensagens.forEach(msg => {
                    adicionarMensagem(msg, mensagensArea);
                    ultimaMensagemId = Math.max(ultimaMensagemId, msg.id);
                });
                scrollToBottom();
            }
        })
        .catch(error => {
            console.error('Erro ao buscar mensagens:', error);
        });
}

// Função para enviar mensagem
function enviarMensagem() {
    const form = document.getElementById('form-enviar-mensagem');
    const input = document.getElementById('input-mensagem');
    const mensagem = input.value.trim();
    
    if (!mensagem) return;
    
    const formData = new FormData(form);
    
    fetch('enviar_mensagem.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Limpa o input
            input.value = '';
            
            // Adiciona a mensagem à área
            const mensagensArea = document.getElementById('mensagens-area');
            adicionarMensagem(data.mensagem, mensagensArea);
            
            ultimaMensagemId = Math.max(ultimaMensagemId, data.mensagem.id);
            
            // Scroll para o final
            scrollToBottom();
            
            // Atualiza a lista de conversas (opcional)
            atualizarListaConversas();
        } else {
            alert('Erro ao enviar mensagem: ' + (data.message || 'Erro desconhecido'));
        }
    })
    .catch(error => {
        console.error('Erro ao enviar mensagem:', error);
        alert('Erro ao enviar mensagem. Tente novamente.');
    });
}

// Função para adicionar mensagem na tela
function adicionarMensagem(msg, container) {
    // O ID do usuário logado precisa ser definido na página
    const usuarioLogadoId = window.usuarioLogadoId || 0;
    const ehMinha = msg.remetente_id == usuarioLogadoId;
    const nomeExibir = ehMinha ? 'Eu' : msg.nome_usuario;
    const fotoExibir = msg.foto_perfil;
    
    const mensagemDiv = document.createElement('div');
    mensagemDiv.className = `mensagem-item ${ehMinha ? 'minha-mensagem' : 'outra-mensagem'}`;
    mensagemDiv.dataset.mensagemId = msg.id;
    
    let html = '';
    
    if (!ehMinha) {
        html += `<img src="login/uploads/${msg.foto_perfil}" alt="${msg.nome_usuario}" class="mensagem-avatar">`;
    }
    
    // Escapa HTML para prevenir XSS
    const conteudoEscapado = msg.conteudo
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;')
        .replace(/\n/g, '<br>');
    
    const nomeEscapado = nomeExibir
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    
    html += `
        <div class="mensagem-bubble">
            <span class="mensagem-remetente">${nomeEscapado}</span>
            <p class="mensagem-conteudo">${conteudoEscapado}</p>
        </div>
    `;
    
    if (ehMinha) {
        let statusIcon = '';
        if (msg.status === 'lida') {
            statusIcon = '<i class="fa-solid fa-check-double" style="color: #008EE0;"></i>';
        } else if (msg.status === 'entregue') {
            statusIcon = '<i class="fa-solid fa-check-double"></i>';
        } else {
            statusIcon = '<i class="fa-solid fa-check"></i>';
        }
        
        const minhaFoto = window.usuarioLogadoFoto || '';
        html += `
            <div class="mensagem-meta">
                <span class="mensagem-hora">${msg.hora}</span>
                ${statusIcon}
            </div>
            <img src="login/uploads/${minhaFoto}" alt="Meu perfil" class="mensagem-avatar">
        `;
    } else {
        html += `
            <div class="mensagem-meta">
                <span class="mensagem-hora">${msg.hora}</span>
            </div>
        `;
    }
    
    mensagemDiv.innerHTML = html;
    container.appendChild(mensagemDiv);
}

// Função para fazer scroll até o final
function scrollToBottom() {
    const mensagensArea = document.getElementById('mensagens-area');
    if (mensagensArea) {
        mensagensArea.scrollTop = mensagensArea.scrollHeight;
    }
}

// Função para atualizar lista de conversas (opcional - pode recarregar página ou atualizar via AJAX)
function atualizarListaConversas() {
    // Pode implementar atualização dinâmica da lista se necessário
    // Por enquanto, apenas atualiza quando recarregar
}

// Limpa o intervalo quando sair da página
window.addEventListener('beforeunload', function() {
    if (intervalId) {
        clearInterval(intervalId);
    }
});

// Busca conversas ao digitar na pesquisa
const pesquisaConversas = document.getElementById('pesquisa-conversas');
if (pesquisaConversas) {
    pesquisaConversas.addEventListener('input', function() {
        const termo = this.value.toLowerCase().trim();
        const conversas = document.querySelectorAll('.conversa-item');
        
        conversas.forEach(conversa => {
            const nome = conversa.querySelector('.conversa-nome')?.textContent.toLowerCase() || '';
            const preview = conversa.querySelector('.conversa-preview')?.textContent.toLowerCase() || '';
            
            if (nome.includes(termo) || preview.includes(termo)) {
                conversa.style.display = 'flex';
            } else {
                conversa.style.display = 'none';
            }
        });
    });
}

