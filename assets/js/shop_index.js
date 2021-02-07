require('../scss/shop_index.scss');

const itemContainer = document.querySelector('.item-container');
const prototype = itemContainer.querySelector('.item-prototype');
const flash = document.querySelector('.flash');

document.addEventListener('DOMContentLoaded', () => {
    initFetchCategory();
    heroAnimation();
})

const heroAnimation = () => {
    const svg = document.querySelector('svg');
    document.querySelector('.hero').addEventListener('mousemove', (e) => {
        svg.style.left = e.clientX;
    })
    document.querySelector('.hero').addEventListener('mouseleave', () => {
        svg.style.left = '50%';
    })
}

const initFetchCategory = () => {
    const categoryLinks = Array.from(document.querySelectorAll('.category-container .category'));

    for (const category of categoryLinks) {
        category.addEventListener('click', (e) => {
            e.preventDefault();

            if (itemContainer.classList.contains('loading')) return;

            const url = e.target.href;
            itemContainer.classList.add('loading');

            fetch(url, {
                headers: {
                    'Content-Type': 'application/json',
                }
            }).then(res => {
                if (res.ok) {
                    res.json().then(data => {
                        const items = data.items?.map(item => JSON.parse(item));

                        while (itemContainer.querySelector('.item')) {
                            itemContainer.removeChild(itemContainer.querySelector('.item'));
                        }

                        if (items) {
                            flash.classList.add('disabled')

                            for (let i = 0; i < items.length; i++) {
                                const clone = prototype.cloneNode(true);
                                let link = clone.querySelector('a');

                                link.href = link.getAttribute('href').replace(/slug/gi, items[i].slug);
                                clone.querySelector('.title').innerText = items[i].name;
                                clone.querySelector('img').src = data['covers'][i];
                                clone.querySelector('.price').innerText = priceFormatter(items[i].price);
                                clone.classList.replace('item-prototype', 'item');

                                if (!items[i].sold) {
                                    clone.querySelector('.sold-title').remove();
                                }

                                itemContainer.appendChild(clone);
                            }
                        } else {
                            flash.classList.remove('disabled');
                        }

                        itemContainer.classList.remove('loading');
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

const priceFormatter = (int) => {
    const formatter = new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR',
    });

    return formatter.format(int);
}
