export function strToDom(str) {
    return document.createRange().createContextualFragment(str)
}


/**
 * @param e
 * @param {HTMLElement} target
 * @returns {*}
 */
export const isTarget = (e, target) => {
    return e.composedPath().includes(target)
}