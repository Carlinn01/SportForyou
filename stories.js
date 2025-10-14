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

    // Cancela timeout anterior
    if (timeoutId) clearTimeout(timeoutId);

    // Atualiza conteúdo
    content.innerHTML = type === 'video'
        ? `<video src="${media}" autoplay muted></video>`
        : `<img src="${media}" alt="story">`;

    // Exibe viewer
    viewer.classList.remove('hidden');

    // Reseta e anima progress bar
    progress.style.transition = 'none';
    progress.style.width = '0%';
    setTimeout(() => {
        progress.style.transition = 'width 5s linear';
        progress.style.width = '100%';
    }, 50);

    // Timeout para fechar story automaticamente
    timeoutId = setTimeout(() => {
        viewer.classList.add('hidden');
        content.innerHTML = '';
        timeoutId = null;
    }, 5000);
}

// Clique em um story
stories.forEach((story, i) => {
    story.addEventListener('click', () => showStory(i));
});

// Clique no viewer (lado esquerdo/direito)
viewer.addEventListener('click', (e) => {
    const x = e.clientX;
    const width = viewer.offsetWidth;

    // Evita que clique no botão feche o viewer
    if (e.target === closeBtn || e.target === navLeft || e.target === navRight) return;

    if (x < width / 2) {
        if (currentIndex > 0) showStory(currentIndex - 1);
    } else {
        if (currentIndex < storiesArray.length - 1) showStory(currentIndex + 1);
    }
});

// Fechar com X
closeBtn.addEventListener('click', () => {
    viewer.classList.add('hidden');
    content.innerHTML = '';
    if (timeoutId) clearTimeout(timeoutId);
    timeoutId = null;
});

// Navegação com barras clicáveis
navLeft.addEventListener('click', () => {
    if (currentIndex > 0) showStory(currentIndex - 1);
});

navRight.addEventListener('click', () => {
    if (currentIndex < storiesArray.length - 1) showStory(currentIndex + 1);
});

// Navegação com teclado
document.addEventListener('keydown', (e) => {
    if (viewer.classList.contains('hidden')) return;

    if (e.key === 'ArrowLeft' && currentIndex > 0) {
        showStory(currentIndex - 1);
    } else if (e.key === 'ArrowRight' && currentIndex < storiesArray.length - 1) {
        showStory(currentIndex + 1);
    } else if (e.key === 'Escape') {
        viewer.classList.add('hidden');
        content.innerHTML = '';
        if (timeoutId) clearTimeout(timeoutId);
        timeoutId = null;
    }
});


const input = document.getElementById('search-input');
const resultsContainer = document.getElementById('search-results');

input.addEventListener('input', async () => {
    const query = input.value.trim();

    if (query.length === 0) {
        resultsContainer.style.display = 'none';
        resultsContainer.innerHTML = '';
        return;
    }

    const res = await fetch(`buscar.php?q=${encodeURIComponent(query)}`);
    const data = await res.json();

    resultsContainer.innerHTML = '';

    if (data.length > 0) {
        data.forEach(item => {
            const div = document.createElement('div');
            div.classList.add('search-item');

            if (item.tipo === 'usuario') {
                div.dataset.userId = item.id;
                div.innerHTML = `<img src="login/uploads/${item.foto_perfil}" alt="${item.nome_usuario}">
                                 <span>@${item.nome_usuario}</span>`;
            } else if (item.tipo === 'postagem') {
                div.dataset.postId = item.id;
                div.innerHTML = `<p>${item.texto.substring(0, 50)}...</p>`;
            }

            resultsContainer.appendChild(div);
        });
        resultsContainer.style.display = 'block';
    } else {
        resultsContainer.style.display = 'none';
    }
});

resultsContainer.addEventListener('click', (e) => {
    const user = e.target.closest('.search-item[data-user-id]');
    const post = e.target.closest('.search-item[data-post-id]');

    if (user) {
        const id = user.dataset.userId;
        window.location.href = `perfil.php?id=${id}`;
    } else if (post) {
        const id = post.dataset.postId;
        window.location.href = `postagem.php?id=${id}`;
    }
});
