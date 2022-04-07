import TomSelect from "tom-select";
import {jsonFetch} from "./fetch";

/**
 * @param {string} selector
 * @param parent
 * @return {HTMLElement}
 */
export function $(selector, parent = document) {
    return parent.querySelector(selector);
}

/**
 * @param {string} selector
 * @param parent
 * @return {HTMLElement[]}
 */
export function $$(selector, parent = document) {
    return [...parent.querySelectorAll(selector)]
}

/**
 * Transform une chaîne en élément DOM
 * @param {string} str
 * @return {DocumentFragment}
 */
export function strToDom(str) {
    return document.createRange().createContextualFragment(str).firstChild;
}

export function bindCreateOptionSelect(select) {
    const url = select.dataset.createOption

    let headers = new Headers()
    headers.append('Content-Type', "application/json")
    headers.append('Accept', "application/json")

    new TomSelect(select, {
        create: true,
        onOptionAdd: async (value, data) => {
            let response = await jsonFetch(url, {
                method: "POST",
                headers: headers,
                body: JSON.stringify({name: value})
            })
            console.log(response)
            console.log(data)
            data.$option.value = response.id
        }
    })
}