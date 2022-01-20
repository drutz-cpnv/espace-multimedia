import './styles/admin.css';
import './bootstrap';
import './controllers/activePage'
import TomSelect from "tom-select";


let multiselect = Array.from(document.querySelectorAll("select[multiple]"))

if (multiselect.length >= 1) {
    multiselect.map(v => {
        new TomSelect(v, {})
    })
}