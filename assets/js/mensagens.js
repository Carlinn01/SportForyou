// Sistema de mensagens em tempo real

let intervalId = null;
let ultimaMensagemId = 0;

// Função para voltar à lista de conversas (mobile)
function voltarParaConversas() {
    const conversasPanel = document.querySelector('.conversas-panel');
    const chatPanel = document.querySelector('.chat-panel');
    
    if (window.innerWidth <= 768) {
        if (conversasPanel) {
            conversasPanel.classList.remove('hidden');
        }
        if (chatPanel) {
            chatPanel.classList.remove('active');
        }
    }
}

// Função para mostrar chat (mobile)
function mostrarChat() {
    const conversasPanel = document.querySelector('.conversas-panel');
    const chatPanel = document.querySelector('.chat-panel');
    
    if (window.innerWidth <= 768) {
        if (conversasPanel) {
            conversasPanel.classList.add('hidden');
        }
        if (chatPanel) {
            chatPanel.classList.add('active');
        }
    }
}

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
        
        // Se há conversa selecionada, mostra o chat no mobile
        if (conversaId && window.innerWidth <= 768) {
            mostrarChat();
        }
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
    
    // Adiciona evento de clique nas conversas para mobile
    const conversaItems = document.querySelectorAll('.conversa-item');
    conversaItems.forEach(item => {
        item.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                // No mobile, mostra o chat após um pequeno delay para permitir navegação
                setTimeout(function() {
                    const chatPanel = document.querySelector('.chat-panel');
                    if (chatPanel && chatPanel.querySelector('.chat-header')) {
                        mostrarChat();
                    }
                }, 100);
            }
        });
    });
    
    // Ajusta layout ao redimensionar janela
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            const conversasPanel = document.querySelector('.conversas-panel');
            const chatPanel = document.querySelector('.chat-panel');
            if (conversasPanel) {
                conversasPanel.classList.remove('hidden');
            }
            if (chatPanel) {
                chatPanel.classList.remove('active');
            }
        }
    });
});

// Função para buscar novas mensagens
function buscarNovasMensagens(conversaId) {
    fetch(`../api/buscar_mensagens.php?conversa_id=${conversaId}&ultima_mensagem_id=${ultimaMensagemId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.mensagens.length > 0) {
                const mensagensArea = document.getElementById('mensagens-area');
                data.mensagens.forEach(msg => {
                    adicionarMensagem(msg, mensagensArea);
                    ultimaMensagemId = Math.max(ultimaMensagemId, msg.id);
                });
                scrollToBottom();
                
                // Atualiza status das mensagens após um breve delay
                setTimeout(() => {
                    atualizarStatusMensagens();
                }, 500);
            }
        })
        .catch(error => {
            console.error('Erro ao buscar mensagens:', error);
        });
}

// Função para atualizar status das mensagens lidas
function atualizarStatusMensagens() {
    const mensagensMinhas = document.querySelectorAll('.mensagem-item.minha-mensagem');
    mensagensMinhas.forEach(mensagemEl => {
        const mensagemId = mensagemEl.dataset.mensagemId;
        const checkIcon = mensagemEl.querySelector('.fa-check-double:not(.mensagem-lida)');
        if (checkIcon) {
            fetch(`../api/buscar_status_mensagem.php?id=${mensagemId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.status === 'lida') {
                        checkIcon.classList.add('mensagem-lida');
                    }
                })
                .catch(err => console.error('Erro ao atualizar status:', err));
        }
    });
}

// Função para enviar mensagem
function enviarMensagem() {
    const form = document.getElementById('form-enviar-mensagem');
    const input = document.getElementById('input-mensagem');
    const inputAnexo = document.getElementById('input-anexo');
    const mensagem = input.value.trim();
    const anexo = inputAnexo.files[0];
    
    if (!mensagem && !anexo) return;
    
    const formData = new FormData(form);
    
    fetch('../api/enviar_mensagem.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Limpa o input e anexo
            input.value = '';
            if (inputAnexo) {
                inputAnexo.value = '';
            }
            
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
        html += `<img src="../login/uploads/${msg.foto_perfil}" alt="${msg.nome_usuario}" class="mensagem-avatar">`;
    }
    
    // Escapa HTML para prevenir XSS
    let conteudoEscapado = msg.conteudo
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;')
        .replace(/\n/g, '<br>');
    
    // Adiciona anexo se houver
    if (msg.anexo_url) {
        const anexoUrl = msg.anexo_url;
        if (anexoUrl.match(/\.(jpg|jpeg|png|gif|webp)$/i)) {
            conteudoEscapado += `<br><img src="../login/uploads/${anexoUrl}" alt="Anexo" class="mensagem-anexo-imagem" onclick="window.open('../login/uploads/${anexoUrl}', '_blank')">`;
        } else if (anexoUrl.match(/\.(mp4|mov|quicktime)$/i)) {
            conteudoEscapado += `<br><video controls class="mensagem-anexo-video"><source src="../login/uploads/${anexoUrl}" type="video/mp4"></video>`;
        } else {
            const nomeArquivo = anexoUrl.split('/').pop();
            conteudoEscapado += `<br><a href="../login/uploads/${anexoUrl}" target="_blank" class="mensagem-anexo-link">📎 ${nomeArquivo}</a>`;
        }
    }
    
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
            statusIcon = '<i class="fa-solid fa-check-double mensagem-lida"></i>';
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
            <img src="../login/uploads/${minhaFoto}" alt="Meu perfil" class="mensagem-avatar">
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

// Emoji Picker
const btnEmoji = document.getElementById('btn-emoji-mensagens');
const emojiPicker = document.getElementById('emoji-picker-mensagens');
const inputMensagem = document.getElementById('input-mensagem');

if (btnEmoji && emojiPicker) {
    btnEmoji.addEventListener('click', function(e) {
        e.stopPropagation();
        emojiPicker.classList.toggle('show');
    });

    // Fecha emoji picker ao clicar fora
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.emoji-picker-container') && !e.target.closest('.btn-emoji')) {
            emojiPicker.classList.remove('show');
        }
    });

    // Adiciona emoji ao input
    const emojiItems = emojiPicker.querySelectorAll('.emoji-item');
    emojiItems.forEach(item => {
        item.addEventListener('click', function() {
            const emoji = this.dataset.emoji;
            inputMensagem.value += emoji;
            inputMensagem.focus();
            emojiPicker.classList.remove('show');
        });
    });
}

// Upload de arquivo
const btnAnexo = document.getElementById('btn-anexo-mensagens');
const inputAnexo = document.getElementById('input-anexo');

if (btnAnexo && inputAnexo) {
    btnAnexo.addEventListener('click', function() {
        inputAnexo.click();
    });

    inputAnexo.addEventListener('change', function() {
        if (this.files && this.files.length > 0) {
            const file = this.files[0];
            const maxSize = 10 * 1024 * 1024; // 10MB
            
            if (file.size > maxSize) {
                alert('Arquivo muito grande. Máximo: 10MB');
                this.value = '';
                return;
            }
            
            // Mostra preview ou nome do arquivo
            console.log('Arquivo selecionado:', file.name);
        }
    });
}

