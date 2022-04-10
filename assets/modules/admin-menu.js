import {$, strToDom} from "../functions/dom";

export const menu = () => {
    const toggleBtn = $(".mobile-menu-toggle")
    const header = $("aside")
    const icon = toggleBtn.querySelector(".icon")

    const mainContent = $(".main-content")
    mainContent.append(strToDom(`<div class="menu-overlay"></div>`))
    const overlay = $(".menu-overlay")
    overlay.classList.add("hide")

    const SETTINGS = {
        open: "close",
        close: "menu"
    }

    let state = false

    const handleClick = (e) => {
        e.preventDefault()
        state = !state
        header.classList.toggle("is-open")
        if(state) {
            icon.textContent = SETTINGS.open
            overlay.classList.remove("hide")
        }
        else{
            icon.textContent = SETTINGS.close
            overlay.classList.add("hide")
        }
    }

    overlay.addEventListener("click", handleClick)

    toggleBtn.addEventListener("click", handleClick)

};
