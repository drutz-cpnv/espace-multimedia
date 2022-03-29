/**
 * @param {Number} userId
 * @param {string} endpoint
 */
import {strToDom} from "../functions/dom";
import feather from "feather-icons";

const verify = async (userId, endpoint = "/admin/utilisateurs/verify/") => {
    let response = await fetch(`${endpoint}${userId}`)
    return await response.json()
};

export const initValidation = () => {
    const $studentsElements = Array.from(document.querySelectorAll("[data-student]"))

    if($studentsElements.length === 0) return

    $studentsElements.forEach(el => {
        const $btn = el.querySelector("[data-verify-btn]")
        const $indicator = el.querySelector("[data-verification-indicator]")
        $indicator.append()
        const userId = parseInt(el.dataset.student)
        $btn.addEventListener("click", e => {
            $indicator.append(strToDom(`<progress-bar></progress-bar>`))
            const $bar = $indicator.querySelector("progress-bar")
            e.preventDefault()
            $bar.style.setProperty('--progress', "0")
            $bar.style.setProperty('--duration', "5s")
            $bar.style.setProperty('--progress', "70%")
            verify(userId).then(({verification}) => {
                console.log(verification)
                $bar.style.setProperty('--duration', ".2s")
                $bar.style.setProperty('--progress', "100%")
                setTimeout(() => {
                    $bar.style.opacity = 0
                    setTimeout(() => {
                        $bar.remove()
                        $indicator.innerHTML = "";
                        if(verification.result === 'valid') {
                            $indicator.append(strToDom(`<span style="color: #00e500; display: flex; align-items: center;" class="font-mono"><i data-feather="check-circle"></i> <span class="ml2" style="height: fit-content">CPNV APPROUVED</span></span>`))
                        }
                        else {
                            $indicator.append(strToDom(`<span style="color: #e50000; display: flex; align-items: center;" class="font-mono"><i data-feather="x-circle"></i> <span class="ml2" style="height: fit-content">CPNV REJECTED</span></span>`))
                        }
                        feather.replace()
                    }, 200)
                }, 1000)
            })
        })
    })
}