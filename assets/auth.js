import registerForm from "./modules/auth";
import Alert from "./modules/alert";
import feather from "feather-icons";
import {menu} from "./modules/menu";

menu()

customElements.define('alert-block', Alert)

feather.replace()


registerForm()
