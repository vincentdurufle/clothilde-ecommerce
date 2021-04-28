require('../scss/item_show.scss');
import KeenSlider from "keen-slider";
const $ = require('jquery');

const addBtn = document.querySelector('.add-item');

document.addEventListener('DOMContentLoaded', () => {
    initSlider();
    if (addBtn) {
        addBtn.addEventListener('click', addItem);
    }
})

const addItem = (e) => {
    e.preventDefault();

    if (!addBtn.classList.contains('loading') || !addBtn.classList.contains('btn-success') || !addBtn.classList.contains('error')) {
        addBtn.classList.add('loading');

        fetch(e.target.href).then(res => {
            if (res.ok) {
                $('.toast').toast('show');
                addBtn.classList.replace('loading', 'btn-success');
                addBtn.innerHTML = '<i class="far fa-check-circle"></i>';

                return;
            }

            throw new Error();
        }).catch(err => {
            console.error(err);
            addBtn.classList.replace('loading', 'error');
            $('.toast').toast('show');
        })
    }
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
