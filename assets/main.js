import TomSelect from "tom-select";

let multiselect = Array.from(document.querySelectorAll("select[multiple]"))

if (multiselect) {
    multiselect.map(v => {
        new TomSelect(v, {})
    })
}