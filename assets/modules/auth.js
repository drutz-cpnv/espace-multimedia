import {strToDom} from "../functions/dom";

const registerForm = () => {
    let form = document.querySelector(".auth-form form")
    console.log(form)
    form.innerHTML = ""
    form.append(strToDom(`<div class="spinner">
    <span></span>
</div>`))
    form.addEventListener("submit", (e) => {
        e.preventDefault()
        let loading = strToDom(`<div class="spinner">
    
</div>`)
    })
}

export default registerForm