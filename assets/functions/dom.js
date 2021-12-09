/**
 * @param {string} selector
 * @return {HTMLElement}
 */
export function $(selector) {
    return document.querySelector(selector);
}

/**
 * @param {string} selector
 * @return {HTMLElement[]}
 */
export function $$(selector) {
    return Array.from(document.querySelectorAll(selector));
}

/**
 * Transform une chaine en élément DOM
 * @param {string} str
 * @return {DocumentFragment}
 */
export function strToDom(str) {
    return document.createRange().createContextualFragment(str).firstChild;
}