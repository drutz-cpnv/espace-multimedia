import './styles/app.scss';
import './bootstrap';
import './controllers/activePage'
import './main'
import inputTooltip from "./modules/InputTooltip";
import {registerWindowHeightCSS} from "./modules/window";
import {menu} from "./modules/menu";
inputTooltip()

registerWindowHeightCSS()


document.documentElement.addEventListener("turbo:load", evt => {

    menu()

})