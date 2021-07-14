require('../scss/collaboration_index.scss');

document.addEventListener('DOMContentLoaded', () => {
    window.addEventListener('scroll', circleScroll)
})

const circleScroll = () => {
    const header = document.querySelector('.header');
    const circle = document.querySelector('.animation-line .fa-circle');

    if (-header.getBoundingClientRect().top > header.offsetHeight) {
        return;
    }

    circle.style.top = `calc(${-header.getBoundingClientRect().top}px + 20%)`;
}