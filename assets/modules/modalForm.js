import {strToDom} from "./polyfill";

class ModalForm {

    URI

    /**
     * @param {HTMLLinkElement} button
     */
    constructor(button) {
        this.button = button
        this.URI = this.button.href
        this.button.addEventListener("click", this.handleButtonClick)

    }

    handleButtonClick = (e) => {
        e.preventDefault()
        this.callForm()
    }

    async callForm() {
        let response = await fetch(this.URI)
        let result = await response.text()
        this.modal = strToDom(`<div class="admin-order-modal visible">
<div class="modal-content">
<div class="close-btn"></div>
${result.toString()}
</div>
</div>`)
        this.modal.querySelector(".close-btn").addEventListener("click", (e) => {
            e.target.parentElement.parentElement.remove()
        })
        document.body.append(this.modal)
    }

}

export default ModalForm