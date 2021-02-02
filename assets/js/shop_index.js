require('../scss/shop_index.scss');

document.addEventListener('DOMContentLoaded', () => {
    initFetchCategory();
})

const initFetchCategory = () => {
    const categoryLinks = Array.from(document.querySelectorAll('.category-container .category'));

    for (const category of categoryLinks) {
        category.addEventListener('click', (e) => {
            e.preventDefault();

            const url = e.target.href;

            fetch(url, {
                headers: {
                    'Content-Type': 'application/json',
                }
            }).then(res => {
                if (res.ok) {
                    res.json().then(data => {
                        console.log(JSON.parse(data['items']));
                    })
                } else {
                    console.error('smth happened')
                }
            }).catch(err => {
                console.error(err);
            })
        })
    }
}