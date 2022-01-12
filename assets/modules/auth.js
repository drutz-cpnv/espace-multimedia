import {strToDom} from "../functions/dom";

const successMessage = strToDom(`<div>
<div class="text-center text-big text-success success">
    <span class="material-icons-round">done</span><h3>Compte créé</h3>
</div>
<div class="text-center text-muted"><span>Veuillez dès a présent le vérifier en vous rendant dans vos mail et en cliquant sur le lien prévu à cet effet.</span></div></div>`)

const errorMessage = strToDom(`<div>
<div class="text-center text-big text-danger error">
    <span class="material-icons-round">priority_high</span><h3>Une erreur est survenue</h3>
</div>
<div class="text-center text-muted"><span>Si vous pensez que c'est une erreur, veuillez contacter le webmaster.</span></div></div>`)


let loader = strToDom(`<div><div class="text-center text-big"><span>Recherche en cours dans la base de donnée du CPNV...</span></div>
    <div class="spinner" style="--size: 30px; margin-top: 15px">
        <span></span>
    </div></div>`)

const registerForm = () => {
    let form = document.querySelector(".auth-form form")

    form.addEventListener("submit", (e) => {
        e.preventDefault()
        let formData = new FormData(form)
        form.classList.add("no-visible")
        form.parentElement.append(loader)
        connectionCall(formData, form)
    })
}

const connectionCall = async (formData, form) => {
    let response = await fetch('/inscription', {
        method: 'POST',
        body: formData
    })

    let result = await response.json()

    if(result.type === "success"){
        form.replaceWith(successMessage)
        loader.remove()
    }
    else if(result.type === "error"){
        form.replaceWith(errorMessage)
        loader.remove()
    }


}

export default registerForm