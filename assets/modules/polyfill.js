export function strToDom(str) {
    return document.createRange().createContextualFragment(str)
}


export const isTarget = (e, target) => {
    return e.path.includes(target)
}