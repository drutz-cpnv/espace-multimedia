class Icon extends HTMLElement {


    iconName

    constructor() {
        super();
        this.iconName = this.getAttribute('icon-name')
        let iconClass = this.getAttribute('rounded') ?? true
    }

    connectedCallback() {

    }
}