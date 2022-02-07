import TomSelect from "tom-select";
import ModalForm from "./modules/modalForm";
import {Cart} from "./modules/cart";



document.documentElement.addEventListener("turbo:load", evt => {
    let multiselect = Array.from(document.querySelectorAll("select[multiple]"))

    if (multiselect) {
        multiselect.map(v => {
            new TomSelect(v, {})
        })
    }

    const cartContainer = document.querySelector(".header__cart")

    if(cartContainer) {
        let cart = new Cart(cartContainer)
    }

    let modalButtons = document.querySelectorAll(".js-modal-btn")

    if(modalButtons){
        modalButtons = Array.from(modalButtons)
        modalButtons.map(v => {
            let modal = new ModalForm(v)
        })
    }
})

