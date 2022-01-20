const currentPage = () => {
    let currentPageElementMenu = document.querySelector(".nav-element[aria-current]") || document.querySelector(".nav-btn[aria-current]")

    if (currentPageElementMenu) {
        currentPageElementMenu.classList.add("is-active")
    }
}


export default currentPage()