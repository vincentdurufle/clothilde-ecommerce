require('../scss/item_show.scss');

import KeenSlider from "keen-slider";

document.addEventListener('DOMContentLoaded', () => {
    initSlider();
    document.querySelector('#checkout-button').addEventListener('click', checkout)
})

const checkout = (e) => {
    const stripe = Stripe(e.target.dataset.stripe);
    fetch(e.target.dataset.href, {
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

const initSlider = () => {
    let slider = new KeenSlider("#keen-slider", {
        created:  (instance) => {
            document
                .getElementById("arrow-left")
                .addEventListener("click", () => instance.prev());

            document
                .getElementById("arrow-right")
                .addEventListener("click", () => instance.next());

            const dotsWrapper = document.getElementById("dots");
            const slides = document.querySelectorAll(".keen-slider__slide");
            slides.forEach((t, idx) => {
                const dot = document.createElement("button")
                dot.classList.add("dot")
                dotsWrapper.appendChild(dot)
                dot.addEventListener("click", function () {
                    instance.moveToSlide(idx)
                })
            })
            updateClasses(instance)
        },
        slideChanged(instance) {
            updateClasses(instance)
        },
    })


}
const updateClasses = (instance) => {
    const slide = instance.details().relativeSlide
    const arrowLeft = document.getElementById("arrow-left")
    const arrowRight = document.getElementById("arrow-right")
    slide === 0
        ? arrowLeft.classList.add("arrow--disabled")
        : arrowLeft.classList.remove("arrow--disabled")
    slide === instance.details().size - 1
        ? arrowRight.classList.add("arrow--disabled")
        : arrowRight.classList.remove("arrow--disabled")

    const dots = document.querySelectorAll(".dot")
    dots.forEach(function (dot, idx) {
        idx === slide
            ? dot.classList.add("dot--active")
            : dot.classList.remove("dot--active")
    })
}
