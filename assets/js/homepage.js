import '../scss/homepage.scss';
import './components/full-page-scroll.min';
import Parallax from 'parallax-js';

document.addEventListener('DOMContentLoaded', () => {
    new fullScroll({
        mainElement: "scroll-container",
        displayDots: true,
        dotsPosition: "right",
        animateTime: 0.7,
        animateFunction: "ease",
    });

    const container = document.querySelector('.hero-container');
    const parallaxInstance = new Parallax(container, {
        selector: '.layer',
    });
})