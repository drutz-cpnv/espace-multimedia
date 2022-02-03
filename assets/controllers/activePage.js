
const currentPage = () => {
    document.documentElement.addEventListener("turbo:load", evt => {
        let currentPageElementMenu = document.querySelector(".nav-element[aria-current]") || document.querySelector(".nav-btn[aria-current]")

        if (currentPageElementMenu) {
            currentPageElementMenu.classList.add("is-active")
        }
    })
}


export default currentPage()