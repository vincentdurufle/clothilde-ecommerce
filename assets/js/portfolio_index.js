import KeenSlider from "keen-slider";

require('../scss/portfolio_index.scss');
import Rellax from 'rellax';

document.addEventListener('DOMContentLoaded', () => {
    const rellax = new Rellax('.rellax');
    initKeenSliders();
})

const initKeenSliders = () => {
    const sliders = [
        `#slider1`,
        `#slider2`,
        `#slider3`
    ];

    for (const slider of sliders) {
        const options = {
            slidesPerView: 3,
            mode: 'snap',
            spacing: 15,
            loop: true,
            centered: true,
            breakpoints: {
                '(max-width: 768px)': {
                    slidesPerView: 1
                }
            },
            duration: 1000,
            dragStart: () => {
                autoplay(false)
            },
            dragEnd: () => {
                autoplay(true)
            }
        }
        const sliderInstance = new KeenSlider(slider, options);

        let interval = 0

        const autoplay = (run) => {
            clearInterval(interval)
            interval = setInterval(() => {
                if (run && sliderInstance) {
                    sliderInstance.next()
                }
            }, 3000)
        }
        document.querySelector(slider).addEventListener("mouseover", () => {
            autoplay(false)
        })
        document.querySelector(slider).addEventListener("mouseout", () => {
            autoplay(true)
        })

        autoplay(true)
    }
}

