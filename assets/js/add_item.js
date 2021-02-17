require('../scss/add_item.scss');
const imagesContainer = document.querySelector('#item_images');
const prototype = imagesContainer.dataset.prototype;
let index = imagesContainer.querySelectorAll('input').length;

document.addEventListener('DOMContentLoaded', () => {
    imagesContainer.dataset.index = index.toString();
    document.querySelector('.cover').addEventListener('change', previewFile)
    document.querySelector('.add-image').addEventListener('click', addImage)
    // document.querySelector('form').addEventListener('submit', handleDeleteOnSubmit);
})

const previewFile = (e) => {
    const url = URL.createObjectURL(e.target.files[0]);
    const parent = e.target.closest('.custom-file');
    if (parent.querySelector('.uploaded-image')) {
        parent.querySelector('.uploaded-image').src = url;
    } else {
        const img = `<img src="${url}" class="uploaded-image" alt="">`;
        e.target.closest('.custom-file').insertAdjacentHTML('beforeend', img)
    }
}

const addImage = () => {
    let prototypeClone = prototype.replaceAll('__name__', index);

    imagesContainer.dataset.index = (index + 1).toString();
    imagesContainer.insertAdjacentHTML('beforeend', `<div>${prototypeClone}</div>`)
    document.querySelector(`#item_images_${index}_imageFile_file`).addEventListener('change', previewFile)
    index += 1;
}

const handleDeleteOnSubmit = (e) => {
    const deletions = Array.from(document.querySelectorAll('.vich-image .form-group input:not(#item_coverFile_delete)'));

    if (deletions.length > 0) {
        for (const deletion of deletions) {
            if (deletion.checked) {
                deletion.closest('.vich-image').firstChild.remove();
            }
        }
    }
}