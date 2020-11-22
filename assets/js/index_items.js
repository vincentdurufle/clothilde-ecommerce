require('../scss/index_items.scss');

document.addEventListener('DOMContentLoaded', () => {
    handleDelete()
})

const handleDelete = () => {
    const btns = Array.from(document.querySelectorAll('.delete'));
    for (const btn of btns) {
        btn.addEventListener('click', (e) => {
            if (!confirm('You are about to delete this item, confirm ?')) {
                e.preventDefault()
            }
        })
    }
}