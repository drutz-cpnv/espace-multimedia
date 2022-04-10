import {$} from "../functions/dom";

export const menu = () => {
    const toggleBtn = $(".mobile-menu-toggle")
    const header = $("nav.header")
    const icon = toggleBtn.querySelector(".icon")
    const SETTINGS = {
        open: "close",
        close: "menu"
    }

    let state = false

    toggleBtn.addEventListener("click", (e) => {
        e.preventDefault()
        state = !state
        header.classList.toggle("is-open")
        if(state) {
            icon.textContent = SETTINGS.open
            $(".page-warper").style.overflowY = "hidden";
        }
        else{
            icon.textContent = SETTINGS.close
            $(".page-warper").style.overflowY = "hidden";


        }

    })

};