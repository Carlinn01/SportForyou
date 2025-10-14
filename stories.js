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
const resultsBox = document.getElementById('search-results');

input.addEventListener('input', () => {
    const query = input.value.trim();
    if(query.length === 0) {
        resultsBox.style.display = 'none';
        resultsBox.innerHTML = '';
        return;
    }

    fetch(`buscar.php?q=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
            resultsBox.innerHTML = '';
            if(data.length > 0){
                data.forEach(item => {
                    const li = document.createElement('li');
                    if(item.tipo === 'usuario'){
                        li.innerHTML = `<strong>@${item.nome_usuario}</strong> - ${item.nome}`;
                    } else if(item.tipo === 'postagem'){
                        li.innerHTML = `<span>${item.texto}</span>`;
                    }
                    resultsBox.appendChild(li);
                });
                resultsBox.style.display = 'block';
            } else {
                resultsBox.style.display = 'none';
            }
        });
});
