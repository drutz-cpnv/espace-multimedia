import './styles/admin.scss';
import './bootstrap';
import './controllers/activePage'
import './main'
import {initValidation} from "./modules/studentsVerifier";
import {menu} from "./modules/admin-menu";


document.documentElement.addEventListener("turbo:load", evt => {
    menu()
    initValidation()
})