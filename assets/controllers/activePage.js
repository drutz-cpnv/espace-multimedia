
const currentPage = () => {
    document.documentElement.addEventListener("turbo:load", evt => {
        let currentPageElementsMenu = [...document.querySelectorAll("[aria-current]")]

        currentPageElementsMenu.forEach(el => {
            el.classList.add("is-active")
        })
    })
}


export default currentPage()