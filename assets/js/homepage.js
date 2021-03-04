import '../scss/homepage.scss';
import './components/full-page-scroll.min';
import KeenSlider from "keen-slider";
import Vivus from 'vivus';

const sliderElement = document.querySelector('.keen-slider');

document.addEventListener('DOMContentLoaded', () => {
    new fullScroll({
        mainElement: "scroll-container",
        displayDots: true,
        dotsPosition: "right",
        animateTime: 0.7,
        animateFunction: "ease",
    });

    if (sliderElement) {
        initAutoplaySlider();
    }
    initSvgAnimation();
    handleInputForm();
})

const handleInputForm = () => {
    const formInputs = [...document.querySelectorAll('.contact input, .contact textarea')];
    for (const input of formInputs) {
        input.addEventListener('input', () => {
            if (input.value.length > 0) {
                input.classList.add('filled');

                return
            }
            input.classList.remove('filled');
        })
    }
}

const initSvgAnimation = () => {
    new Vivus('volcano', {
        duration: 300
    }, null)
}

const initAutoplaySlider = () => {
    let interval = 0

    const autoplay = (run) => {
        clearInterval(interval)
        interval = setInterval(() => {
            if (run && slider) {
                slider.next()
            }
        }, 3000)
    }

    const slider = new KeenSlider(sliderElement, {
        loop: true,
        duration: 1000,
        dragStart: () => {
            autoplay(false)
        },
        dragEnd: () => {
            autoplay(true)
        },
    })

    sliderElement.addEventListener("mouseover", () => {
        autoplay(false)
    })
    sliderElement.addEventListener("mouseout", () => {
        autoplay(true)
    })

    autoplay(true)
}