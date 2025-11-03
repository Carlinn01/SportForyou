// ==========================
// STORIES
// ==========================
const stories = document.querySelectorAll('.story-item');
const viewer = document.querySelector('.story-viewer');
const content = document.querySelector('.story-content');
const progress = document.querySelector('.progress-bar');
const closeBtn = document.querySelector('.story-close');
const navLeft = document.querySelector('.nav-left');
const navRight = document.querySelector('.nav-right');

let currentIndex = 0;
let timeoutId = null;
const storiesArray = Array.from(stories);

// Função para deletar story
function deletarStory(idstory) {
    if (!confirm('Tem certeza que deseja deletar este story?')) {
        return;
    }

    fetch('../api/deletar_story.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'idstory=' + idstory
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove o story da lista
            const storyEl = document.querySelector(`[data-story-id="${idstory}"]`);
            if (storyEl) {
                storyEl.style.transition = 'opacity 0.3s';
                storyEl.style.opacity = '0';
                setTimeout(() => {
                    storyEl.remove();
                    // Atualiza a lista de stories
                    storiesArray = Array.from(document.querySelectorAll('.story-item'));
                }, 300);
            }
            alert('Story deletado com sucesso!');
        } else {
            alert('Erro ao deletar story: ' + (data.message || 'Erro desconhecido'));
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao deletar story');
    });
}

function showStory(index) {
    currentIndex = index;
    const story = storiesArray[currentIndex];
    if (!story) return;
    
    const media = story.dataset.media;
    const type = story.dataset.type;
    const storyId = story.dataset.storyId;

    if (timeoutId) clearTimeout(timeoutId);

    // Busca informações do autor do story
    const authorAvatar = story.querySelector('.story-author-avatar');
    const authorName = story.querySelector('.story-author-name');
    const authorUsername = story.querySelector('.story-author-username');
    const storyDate = story.querySelector('.story-date');

    // Atualiza header do viewer
    if (authorAvatar && document.getElementById('viewer-author-avatar')) {
        document.getElementById('viewer-author-avatar').src = authorAvatar.src;
        document.getElementById('viewer-author-name').textContent = authorName ? authorName.textContent : '';
        document.getElementById('viewer-author-username').textContent = authorUsername ? authorUsername.textContent : '';
        document.getElementById('viewer-story-date').textContent = storyDate ? storyDate.textContent : '';
    }

    content.innerHTML = type === 'video'
        ? `<video src="${media}" autoplay muted></video>`
        : `<img src="${media}" alt="story">`;

    viewer.classList.remove('hidden');

    // Progress bar
    progress.style.transition = 'none';
    progress.style.width = '0%';
    setTimeout(() => {
        progress.style.transition = 'width 5s linear';
        progress.style.width = '100%';
    }, 50);

    timeoutId = setTimeout(() => {
        viewer.classList.add('hidden');
        content.innerHTML = '';
        timeoutId = null;
    }, 5000);
}

// Abrir story ao clicar (mas não no botão de deletar)
stories.forEach((story, i) => {
    story.addEventListener('click', (e) => {
        // Não abre se clicar no botão de deletar
        if (e.target.closest('.story-delete-btn')) {
            return;
        }
        showStory(i);
    });
});

// Navegação clicando nas laterais
viewer.addEventListener('click', (e) => {
    const x = e.clientX;
    const width = viewer.offsetWidth;

    if ([closeBtn, navLeft, navRight].includes(e.target)) return;

    if (x < width / 2 && currentIndex > 0) showStory(currentIndex - 1);
    else if (x >= width / 2 && currentIndex < storiesArray.length - 1) showStory(currentIndex + 1);
});

// Fechar story
closeBtn.addEventListener('click', () => {
    viewer.classList.add('hidden');
    content.innerHTML = '';
    if (timeoutId) clearTimeout(timeoutId);
    timeoutId = null;
});

// Botões de navegação
navLeft.addEventListener('click', () => {
    if (currentIndex > 0) showStory(currentIndex - 1);
});
navRight.addEventListener('click', () => {
    if (currentIndex < storiesArray.length - 1) showStory(currentIndex + 1);
});

// Navegação teclado
document.addEventListener('keydown', (e) => {
    if (viewer.classList.contains('hidden')) return;
    if (e.key === 'ArrowLeft' && currentIndex > 0) showStory(currentIndex - 1);
    else if (e.key === 'ArrowRight' && currentIndex < storiesArray.length - 1) showStory(currentIndex + 1);
    else if (e.key === 'Escape') {
        viewer.classList.add('hidden');
        content.innerHTML = '';
        if (timeoutId) clearTimeout(timeoutId);
        timeoutId = null;
    }
});

// ==========================
// PESQUISA
// ==========================
const searchInput = document.querySelector('.search-container input');
const searchResults = document.createElement('div');
searchResults.classList.add('search-results');
document.querySelector('.search-container').appendChild(searchResults);

function limparResultados() {
    searchResults.innerHTML = '';
    searchResults.style.display = 'none';
}

async function buscar(query, filtroEsporte = '', filtroLocalizacao = '') {
    if (!query && !filtroEsporte && !filtroLocalizacao) return limparResultados();

    try {
        let url = `../api/buscar.php?q=${encodeURIComponent(query)}`;
        if (filtroEsporte) url += `&esporte=${encodeURIComponent(filtroEsporte)}`;
        if (filtroLocalizacao) url += `&localizacao=${encodeURIComponent(filtroLocalizacao)}`;
        
        const res = await fetch(url);
        const data = await res.json();

        limparResultados();

        if (data.length === 0) {
            const div = document.createElement('div');
            div.classList.add('search-item');
            div.textContent = 'Nenhum resultado encontrado';
            searchResults.appendChild(div);
        } else {
            data.forEach(item => {
                const div = document.createElement('div');
                div.classList.add('search-item');

                if (item.tipo === 'usuario') {
                    div.dataset.userId = item.id;
                    let localizacaoInfo = '';
                    if (item.cidade || item.estado) {
                        localizacaoInfo = `<small>${item.cidade || ''}${item.cidade && item.estado ? ', ' : ''}${item.estado || ''}</small>`;
                    }
                    div.innerHTML = `
                        <img src="../login/uploads/${item.foto_perfil}" alt="${item.nome}">
                        <div>
                            <span>@${item.nome_usuario}</span>
                            ${localizacaoInfo}
                        </div>
                    `;
                    div.addEventListener('click', () => {
                        window.location.href = `perfil.php?id=${item.id}`;
                    });

                } else if (item.tipo === 'evento') {
                    div.dataset.eventId = item.id;
                    let localizacaoInfo = '';
                    if (item.cidade || item.estado) {
                        localizacaoInfo = `<small>${item.cidade || ''}${item.cidade && item.estado ? ', ' : ''}${item.estado || ''}</small>`;
                    }
                    div.innerHTML = `
                        <div class="evento-icon">📅</div>
                        <div>
                            <strong>${item.titulo}</strong>
                            <small>${item.tipo_esporte}</small>
                            ${localizacaoInfo}
                        </div>
                    `;
                    div.addEventListener('click', () => {
                        window.location.href = `eventos.php#evento-${item.id}`;
                    });
                } else if (item.tipo === 'postagem') {
                    div.dataset.postId = item.id;
                    div.innerHTML = `<p>${item.texto.substring(0, 50)}...</p>`;
                    div.addEventListener('click', () => {
                        window.location.href = `postagem.php?id=${item.id}`;
                    });
                }

                searchResults.appendChild(div);
            });
        }

        searchResults.style.display = 'block';
    } catch (e) {
        console.error('Erro na busca:', e);
    }
}

searchInput.addEventListener('input', () => buscar(searchInput.value.trim()));

// Filtros de busca (se houver interface de filtros)
// Exemplo: buscar com filtros
// buscar('', 'Futebol', 'São Paulo');

// Fecha resultados ao clicar fora
document.addEventListener('click', (e) => {
    if (!document.querySelector('.search-container').contains(e.target)) {
        limparResultados();
    }
});



// notificação

// Função para atualizar badges de notificações e mensagens (apenas animação)
function atualizarBadges() {
    // Conta notificações não lidas
    fetch('../api/contar_notificacoes_nao_lidas.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const badge = document.getElementById('notification-badge');
                if (data.total > 0) {
                    badge.style.display = 'block';
                } else {
                    badge.style.display = 'none';
                }
            }
        })
        .catch(error => console.error('Erro ao contar notificações:', error));

    // Conta mensagens não lidas
    fetch('../api/contar_mensagens_nao_lidas.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const badge = document.getElementById('message-badge');
                if (data.total > 0) {
                    badge.style.display = 'block';
                } else {
                    badge.style.display = 'none';
                }
            }
        })
        .catch(error => console.error('Erro ao contar mensagens:', error));
}

// Atualiza badges ao carregar a página
document.addEventListener('DOMContentLoaded', function() {
    atualizarBadges();
    // Atualiza a cada 30 segundos
    setInterval(atualizarBadges, 30000);
    
    // Re-inicializa stories após carregar
    storiesArray = Array.from(document.querySelectorAll('.story-item'));
});

document.getElementById('bell-icon').addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var notificationsDropdown = document.getElementById('notifications');
        
        // Alterna a visibilidade do menu de notificações
        notificationsDropdown.classList.toggle('show');
    });

// Função para marcar notificação como lida
function marcarNotificacaoLida(idNotificacao) {
    fetch('../api/marcar_notificacao_lida.php?id=' + idNotificacao, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        }
    }).catch(error => {
        console.error('Erro ao marcar notificação como lida:', error);
    });
}



// ==========================
// COMENTAR (AJAX)
// ==========================
document.querySelectorAll('.comment-box form').forEach(form => {
  form.addEventListener('submit', function(event) {
    event.preventDefault(); // Previne o envio padrão do formulário
    
    const commentInput = form.querySelector('.comment-input');
    const idpostagemInput = form.querySelector('input[name="idpostagem"]');
    const commentsList = form.closest('.post-footer').querySelector('.comments-list');
    
    // Verificar se o campo de comentário está vazio
    if (!commentInput.value.trim()) {
      alert("Por favor, escreva um comentário antes de enviar.");
      return;
    }
    
    if (!idpostagemInput) {
      console.error('ID de postagem não encontrado');
      return;
    }
    
    // Prepara dados do formulário
    const formData = new FormData();
    formData.append('idpostagem', idpostagemInput.value);
    formData.append('comentario', commentInput.value.trim());
    
    // Faz requisição AJAX
    fetch('../api/comentar.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success && data.comentario) {
        // Limpa o campo de input
        commentInput.value = '';
        
        // Cria novo elemento de comentário
        const commentDiv = document.createElement('div');
        commentDiv.className = 'comment';
        commentDiv.innerHTML = `
          <img src="../login/uploads/${data.comentario.foto_perfil}" alt="Foto do usuário" width="30" height="30" style="border-radius: 50%; object-fit: cover;">
          <p><strong>${data.comentario.nome_usuario}:</strong> ${data.comentario.comentario}</p>
        `;
        
        // Adiciona o comentário no início da lista
        if (commentsList) {
          commentsList.insertBefore(commentDiv, commentsList.firstChild);
          
          // Atualiza contador de comentários
          const commentCountElement = form.closest('.post-footer').querySelector('.comment-count');
          if (commentCountElement) {
            const currentCount = parseInt(commentCountElement.textContent) || 0;
            commentCountElement.textContent = currentCount + 1;
          }
        }
      } else {
        alert('Erro ao comentar: ' + (data.message || 'Erro desconhecido'));
      }
    })
    .catch(error => {
      console.error('Erro na requisição:', error);
      alert('Erro ao processar comentário. Tente novamente.');
    });
  });
});



// ==========================
// CURTIR POSTAGEM (AJAX)
// ==========================
document.querySelectorAll('.like-btn').forEach(btn => {
  btn.addEventListener('click', function(e) {
    e.preventDefault(); // Previne o comportamento padrão do link
    
    const postagemId = this.dataset.postagem;
    if (!postagemId) return;
    
    const postFooter = this.closest('.post-footer');
    const heartIcon = this.querySelector('i');
    const countElement = postFooter.querySelector('.like-count');
    
    // Faz requisição AJAX
    fetch('../api/curtir.php?idpostagem=' + postagemId, {
      method: 'GET',
      headers: {
        'Accept': 'application/json'
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Atualiza o ícone
        if (data.action === 'liked') {
          heartIcon.classList.remove('fa-regular');
          heartIcon.classList.add('fa-solid');
          heartIcon.style.color = '#e91e63'; // Cor vermelha do coração preenchido
        } else {
          heartIcon.classList.remove('fa-solid');
          heartIcon.classList.add('fa-regular');
          heartIcon.style.color = '';
        }
        
        // Atualiza contador de curtidas
        if (countElement) {
          countElement.textContent = data.curtidas;
        }
      } else {
        console.error('Erro ao curtir:', data.message);
        alert('Erro ao curtir postagem: ' + (data.message || 'Erro desconhecido'));
      }
    })
    .catch(error => {
      console.error('Erro na requisição:', error);
      alert('Erro ao processar curtida. Tente novamente.');
    });
  });
});

// ==========================
// EMOJI PICKER
// ==========================
document.querySelectorAll('.emoji-btn').forEach(btn => {
  btn.addEventListener('click', function(e) {
    e.stopPropagation();
    const picker = this.nextElementSibling;
    if (picker) {
      picker.classList.toggle('hidden');
    }
  });
});

document.querySelectorAll('.emoji-option').forEach(option => {
  option.addEventListener('click', function(e) {
    e.stopPropagation();
    const emoji = this.dataset.emoji;
    const form = this.closest('.comment-form');
    const input = form.querySelector('.comment-input');
    if (input) {
      input.value += emoji;
      input.focus();
    }
    // Fecha o picker
    const picker = this.closest('.emoji-picker');
    if (picker) {
      picker.classList.add('hidden');
    }
  });
});

// Fecha emoji picker ao clicar fora
document.addEventListener('click', function(e) {
  if (!e.target.closest('.comment-input-wrapper')) {
    document.querySelectorAll('.emoji-picker').forEach(picker => {
      picker.classList.add('hidden');
    });
  }
});



