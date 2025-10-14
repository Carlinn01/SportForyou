const stories = document.querySelectorAll('.story');
const viewer = document.querySelector('.story-viewer');
const content = document.querySelector('.story-content');
const progress = document.querySelector('.progress-bar');

stories.forEach(story => {
  story.addEventListener('click', () => {
    const media = story.dataset.media;
    const type = story.dataset.type;

    content.innerHTML = type === 'video'
      ? `<video src="${media}" autoplay muted></video>`
      : `<img src="${media}" alt="story">`;

    viewer.classList.remove('hidden');
    progress.style.width = '0%';
    setTimeout(() => progress.style.width = '100%', 50);

    setTimeout(() => {
      viewer.classList.add('hidden');
      content.innerHTML = '';
    }, 5000); // duração do story
  });
});

viewer.addEventListener('click', () => {
  viewer.classList.add('hidden');
  content.innerHTML = '';
});
