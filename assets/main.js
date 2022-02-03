import TomSelect from "tom-select";
import ModalForm from "./modules/modalForm";



document.documentElement.addEventListener("turbo:load", evt => {
    let multiselect = Array.from(document.querySelectorAll("select[multiple]"))

    if (multiselect) {
        multiselect.map(v => {
            new TomSelect(v, {})
        })
    }

    let modalButtons = document.querySelectorAll(".js-modal-btn")

    if(modalButtons){
        modalButtons = Array.from(modalButtons)
        modalButtons.map(v => {
            let modal = new ModalForm(v)
        })
    }
})

