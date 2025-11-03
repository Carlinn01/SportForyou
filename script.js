// ==========================
// STORIES
// ==========================
const stories = document.querySelectorAll('.story');
const viewer = document.querySelector('.story-viewer');
const content = document.querySelector('.story-content');
const progress = document.querySelector('.progress-bar');
const closeBtn = document.querySelector('.story-close');
const navLeft = document.querySelector('.nav-left');
const navRight = document.querySelector('.nav-right');

let currentIndex = 0;
let timeoutId = null;
const storiesArray = Array.from(stories);

function showStory(index) {
    currentIndex = index;
    const story = storiesArray[currentIndex];
    const media = story.dataset.media;
    const type = story.dataset.type;

    if (timeoutId) clearTimeout(timeoutId);

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

// Abrir story ao clicar
stories.forEach((story, i) => {
    story.addEventListener('click', () => showStory(i));
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

async function buscar(query) {
    if (!query) return limparResultados();

    try {
        const res = await fetch(`buscar.php?q=${encodeURIComponent(query)}`);
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
                    div.innerHTML = `
                        <img src="login/uploads/${item.foto_perfil}" alt="${item.nome}">
                        <span>@${item.nome_usuario}</span>
                    `;
                    div.addEventListener('click', () => {
                        window.location.href = `perfil.php?id=${item.id}`;
                    });

                } else if (item.tipo === 'postagem') {
                    div.dataset.postId = item.id;
                    div.innerHTML = `<p>${item.texto.substring(0, 50)}...</p>`;

                    // 👉 Aqui substituí o redirecionamento
                    div.addEventListener('click', () => {
                        alert(`Postagem encontrada:\n\n${item.texto}`);
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

// Fecha resultados ao clicar fora
document.addEventListener('click', (e) => {
    if (!document.querySelector('.search-container').contains(e.target)) {
        limparResultados();
    }
});



// notificação

document.getElementById('bell-icon').addEventListener('click', function() {
        var notificationsDropdown = document.getElementById('notifications');
        
        // Alterna a visibilidade do menu de notificações
        notificationsDropdown.classList.toggle('show');
    });



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
    fetch('comentar.php', {
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
          <img src="login/uploads/${data.comentario.foto_perfil}" alt="Foto do usuário" width="30" height="30" style="border-radius: 50%; object-fit: cover;">
          <p><strong>${data.comentario.nome_usuario}:</strong> ${data.comentario.comentario}</p>
        `;
        
        // Adiciona o comentário no início da lista
        if (commentsList) {
          commentsList.insertBefore(commentDiv, commentsList.firstChild);
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
    fetch('curtir.php?idpostagem=' + postagemId, {
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



