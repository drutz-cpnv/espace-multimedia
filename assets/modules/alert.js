
export default class Alert extends HTMLElement {



    constructor() {
        super();
        this.header = this.querySelector(".alert-header")
        this.btn = this.querySelector(".alert__minimize-btn")
        this.body = this.querySelector(".alert-body")

        this.isOpen = !this.classList.contains("closed");

        console.log(this.header.clientHeight)

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