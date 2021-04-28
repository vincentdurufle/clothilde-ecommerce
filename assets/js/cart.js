require('../scss/cart.scss');

document.addEventListener('DOMContentLoaded', () => {
    if (!document.querySelector('.empty-cart-container')) {
        handleShipping();
        updateTotal();
        updateSelectedDestinations();
        document.querySelector('.checkout').addEventListener('click', checkout);
        initDelete();
    }
})

const initDelete = () => {
    const buttons = Array.from(document.querySelectorAll('.delete-item'));

    for (const button of buttons) {
        button.addEventListener('click', () => {
            const href = button.dataset.href;
            fetch(href)
                .then(res => {
                    if (res.ok) {
                        window.location.reload();
                    }
                })
        }, {once: true})
    }
}

const checkout = (e) => {
    const stripe = Stripe(e.target.dataset.stripe);
    const shipping = document.querySelector('#destinationSelection option[selected]').value;
    const url = new URL(e.target.dataset.href);

    url.searchParams.append('shipping', shipping);

    fetch(url.href, {
        method: 'POST'
    }).then(res => res.json())
        .then(session => {
            return stripe.redirectToCheckout({ sessionId: session.id });
        })
        .then(result => {
            if (result.error) {
                alert(result.error.message)
            }
        }).catch(err => console.error(err))
}

const updateSelectedDestinations = () => {
    document.querySelector('#destinationSelection').addEventListener('change', (e) => {
        const destination = e.target.value;
        const selectedOptions = Array.from(document.querySelectorAll('.card option[selected]'));
        const optionsToSelect = Array.from(document.querySelectorAll(`.card [data-destination="${destination}"]`));
        e.target.querySelector('option[selected]').removeAttribute('selected');
        e.target.querySelector(`option[value="${destination}"]`).setAttribute('selected', '');

        for (const option of selectedOptions) {
            option.removeAttribute('selected');
        }

        for (const option of optionsToSelect) {
            option.setAttribute('selected', '');
        }

        updateTotal();
    })
}

const updateTotal = () => {
    let total = 0;
    const prices = Array.from(document.querySelectorAll('.item-price, .card option[selected]'));
    for (const price of prices) {
        total += parseInt(price.dataset.price);
    }
    document.querySelector('.total .price').textContent = total.toLocaleString('fr-FR', {
        currency: 'EUR',
        style: 'currency'
    })
}

const handleShipping = () => {
    const unavailableOptions = Array.from(document.querySelectorAll('.card .unavailable[data-destination="eu"], .card .unavailable[data-destination="ww"]'));
    const destinations = {
        eu: false,
        ww: false
    }
    for (const option of unavailableOptions) {
        if (option) {
            const current = option.dataset.destination;
            if (destinations[current] === false) {
                document.querySelector(`#destinationSelection option[value="${current}"]`).remove();
            }
            destinations[current] = true;
        }
        if (destinations.eu === true && destinations.ww === true) {
            return
        }
    }
}
