import {strToDom} from "../functions/dom";

export default class ProgressBar extends HTMLElement {

    constructor() {
        super();
        this.append(strToDom(`<div class="indicator">
</div>`))

    }


}