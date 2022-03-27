import {strToDom} from "../functions/dom";

class Flash extends HTMLElement{

    types = {
        success: {
            color: "#38ce35",
            icon: "check-circle"
        },
        error: {
            color: "#e01313",
            icon: "x-circle"
        }
    }

    constructor() {
        super();
        this.type = this.types[this.getAttribute('type')]
        this.data = {
            message: this.getAttribute('message')
        }
    }

    connectedCallback()
    {
        this.append(this.construct())

        setTimeout(() => {
            this.classList.add("visible")


            let timeOut = setTimeout(this.out, 5000)

            let mouseIsIn = false

            this.addEventListener("mouseenter", e => {
                if(!mouseIsIn) {
                    mouseIsIn = true
                    clearTimeout(timeOut)
                }
            })

            this.addEventListener("mouseleave", e => {
                if(mouseIsIn){
                    timeOut = setTimeout(this.out, 5000)
                    mouseIsIn = false
                }
            })

        }, 100)


    }

    out = () => {
        this.classList.remove("visible")
    }

    construct ()
    {
        return strToDom(`<div style="--flash-color: ${this.type.color}">
    <span>${this.data.message}</span>
</div>`)
    }

}

export default Flash