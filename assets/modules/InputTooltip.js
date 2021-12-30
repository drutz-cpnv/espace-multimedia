const inputTooltip = () => {
    let inputs = Array.from(document.querySelectorAll("input[data-tooltip]"))
    console.log("Hello")
    if(inputs.length > 0) {
        console.log(inputs)
    }
}

export default inputTooltip