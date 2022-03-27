import registerForm from "./modules/auth";
import Alert from "./modules/alert";
import feather from "feather-icons";
customElements.define('alert-block', Alert)

feather.replace()

registerForm()