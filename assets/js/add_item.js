require('../scss/add_item.scss');

document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('.cover').addEventListener('change', previewFile)
})

const previewFile = (e) => {
    const url = URL.createObjectURL(e.target.files[0]);
    const prototype = document.querySelector('.card-prototype').cloneNode(true);

    prototype.querySelector('img').src = url;
    prototype.querySelector('.card-title').textContent = e.target.dataset.title;
    prototype.classList.remove('card-prototype');

    document.querySelector('.previews-container').appendChild(prototype);
}