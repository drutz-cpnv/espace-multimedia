import {strToDom} from "../functions/dom";

const registerForm = () => {
    let form = document.querySelector(".auth-form form")
    let loader = strToDom(`<div><div class="text-center text-big"><span>Recherche en cours dans la base de donnée du CPNV...</span></div>
    <div class="spinner" style="--size: 30px; margin-top: 15px">
        <span></span>
    </div></div>`)
    form.addEventListener("submit", (e) => {
        e.preventDefault()
        let formData = new FormData(form)
        form.classList.add("no-visible")
        form.parentElement.append(loader)
        connectionCall(formData)
    })
}

const connectionCall = async (formData) => {
    let response = await fetch('/inscription', {
        method: 'POST',
        body: formData
    })

    let result = response.json()

    console.log(result)
}

export default registerForm