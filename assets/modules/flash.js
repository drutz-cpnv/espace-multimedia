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


            setTimeout(() => {
                this.classList.remove("visible")
            }, 5000)

        }, 100)


    }

    construct ()
    {
        return strToDom(`<div style="--flash-color: ${this.type.color}">
    <div class="icon">
        <i data-feather="${this.type.icon}"></i>
    </div>
    <span>${this.data.message}</span>
</div>`)
    }

}

export default Flash