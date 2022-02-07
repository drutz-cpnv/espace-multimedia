import {isTarget} from "./polyfill";

export class Cart {

    /**
     * @param {HTMLLIElement} cartHeader
     */
    constructor(cartHeader) {
        this.cartHeader = cartHeader
        this.button = this.cartHeader.querySelector(".js-cart-toggle")
        this.cart = this.cartHeader.querySelector(".js-cart-container")
        this.button.addEventListener("click", this.handleClick)

        document.addEventListener("click", (e) => {
            if(!isTarget(e, this.cart) && this.cart.classList.contains("visible")){
                this.cart.classList.remove("visible")
            }
        })
    }

    handleClick = (e) => {
        e.preventDefault()
        e.stopPropagation()
        this.cart.classList.toggle("visible")
    }


}