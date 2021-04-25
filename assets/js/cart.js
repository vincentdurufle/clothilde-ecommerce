require('../scss/cart.scss');

document.addEventListener('DOMContentLoaded', () => {
    handleShipping();
    updateTotal();
    updateSelectedDestinations();
})

const updateSelectedDestinations = () => {
    document.querySelector('#destinationSelection').addEventListener('change', (e) => {
        const destination = e.target.value;
        const selectedOptions = Array.from(document.querySelectorAll('.card option[selected]'));
        const optionsToSelect = Array.from(document.querySelectorAll(`.card [data-destination="${destination}"]`));
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
