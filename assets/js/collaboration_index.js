require('../scss/collaboration_index.scss');

document.addEventListener('DOMContentLoaded', () => {
    window.addEventListener('scroll', circleScroll, {passive:true})
})

const circleScroll = (e) => {
    console.log(e.getClientRect().top)
}