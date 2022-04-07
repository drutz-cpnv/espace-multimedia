import {strToDom} from "../functions/dom";

export default class Alert extends HTMLElement {

    constructor() {
        super();

        const message = this.getAttribute('message')
        const title = this.getAttribute('alert-title')

        const header = strToDom(`<div class="alert-header">
        <div class="alert__title">
            <span class="icon material-icons-round">warning</span>
            <span class="alert__title-title">${title}</span>
        </div>
        <div class="alert-minimize">
            <a href="#" class="alert__minimize-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </a>
        </div>
    </div>`)

        const body =  strToDom(`<div class="alert-body">
        <p>${message}</p>
    </div>`)


        console.log(body)
        console.log(header)
        this.append(header)
        this.append(body)


        this.header = this.querySelector(".alert-header")
        this.btn = this.querySelector(".alert__minimize-btn")
        this.body = this.querySelector(".alert-body")

        this.isOpen = !this.classList.contains("closed");

        this.defaultHeight = this.header.clientHeight
        const bodyHeight = this.body.clientHeight

        this.openedHeight = this.defaultHeight + bodyHeight

        if(this.isOpen) {
            this.classList.add("open")
            this.style.setProperty('--body-height', `${this.openedHeight}px`)
        } else {
            this.classList.remove("closed")
            this.style.setProperty('--body-height', `${this.defaultHeight}px`)
        }


        this.btn.addEventListener("click", e => {
            e.preventDefault()
            this.toggle()
        })
    }

    connectedCallback() {
    }

    toggle = () => {
        this.classList.toggle("open")
        if(this.isOpen){
            this.close()
        }
        else{
            this.open()
        }
        this.isOpen = !this.isOpen

    }

    open = () => {
        this.style.setProperty('--body-height', `${this.openedHeight}px`)
    }

    close = () => {
        this.style.setProperty('--body-height', `${this.defaultHeight}px`)
    }

}