require('../scss/add_item.scss');
const imagesContainer = document.querySelector('#item_images');
const prototype = imagesContainer.dataset.prototype;
const index = imagesContainer.querySelectorAll('input').length;

document.addEventListener('DOMContentLoaded', () => {
    imagesContainer.dataset.index = index.toString();
    document.querySelector('.cover').addEventListener('change', previewFile)
    document.querySelector('.add-image').addEventListener('click', addImage)
})

const previewFile = (e) => {
    const url = URL.createObjectURL(e.target.files[0]);
    const prototype = document.querySelector('.card-prototype').cloneNode(true);

    prototype.querySelector('img').src = url;
    prototype.querySelector('.card-title').textContent = e.target.dataset.title;
    prototype.classList.remove('card-prototype');

    document.querySelector('.previews-container').appendChild(prototype);
}

const addImage = () => {
    let prototypeClone = prototype.replaceAll('__name__', index);
    imagesContainer.dataset.index = (index + 1).toString();
    imagesContainer.insertAdjacentHTML('beforeend', `<div>${prototypeClone}</div>`)
}