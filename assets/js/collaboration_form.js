require('../scss/collaboration_form.scss');
import pell from "pell";

const imagesContainer = document.querySelector('#collaboration_images');
const prototype = imagesContainer.dataset.prototype;
let index = imagesContainer.querySelectorAll('input').length;

document.addEventListener('DOMContentLoaded', () => {
    imagesContainer.dataset.index = index.toString();
    document.querySelector('.cover').addEventListener('change', previewFile)
    document.querySelector('.add-image').addEventListener('click', addImage)
    initEditor();
    // document.querySelector('form').addEventListener('submit', handleDeleteOnSubmit);
})

const initEditor = () => {
    const textarea = document.querySelector('#collaboration_text');
    pell.init({
        element: document.querySelector('.editor'),

        // <Function>, required
        // Use the output html, triggered by element's `oninput` event
        onChange: html => textarea.value = html,

        // <string>, optional, default = 'div'
        // Instructs the editor which element to inject via the return key
        defaultParagraphSeparator: 'div',

        // <boolean>, optional, default = false
        // Outputs <span style="font-weight: bold;"></span> instead of <b></b>
        styleWithCSS: false,
        // classes<Array[string]> (optional)
        // Choose your custom class names
        classes: {
            actionbar: 'pell-actionbar',
            button: 'pell-button',
            content: 'pell-content',
            selected: 'pell-button-selected'
        }
    })
}

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
    const deletions = Array.from(document.querySelectorAll('.vich-image .form-group input:not(#collaboration_coverFile_delete)'));

    if (deletions.length > 0) {
        for (const deletion of deletions) {
            if (deletion.checked) {
                deletion.closest('.vich-image').firstChild.remove();
            }
        }
    }
}