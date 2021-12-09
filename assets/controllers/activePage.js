const currentPage = () => {
    let currentPageElementMenu = document.querySelector(".nav-element[aria-current]")

    if (currentPageElementMenu) {
        currentPageElementMenu.classList.add("is-active")
    }
}


export default currentPage()