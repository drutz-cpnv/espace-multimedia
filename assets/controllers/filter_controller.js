import { Controller } from 'stimulus';
import {isTarget} from "../modules/polyfill";

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * filter_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {
    connect() {
        this.btn = this.element.querySelector(".js-toggle-filter")
        this.container = this.element.querySelector(".filter")
        document.addEventListener("click", e => {
            if(!isTarget(e, this.element)) {
                this.close()
            }
        })
        this.btn.addEventListener('click', this.handleClick)
    }

    close = () => {
        this.element.classList.remove('open')
    }

    handleClick = (e) => {
        e.preventDefault()
        this.element.classList.toggle('open')
        console.log(e)
    }
}
