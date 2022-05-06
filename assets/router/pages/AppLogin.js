import AppCard from '../../libs/bootstrap-5/AppCard'
import {h} from 'vue'

function AppLogin() {
    return h('div', {class: 'row'}, h(AppCard, {title: 'Connexion'}, () => 'Connexion'))
}

AppLogin.displayName = 'AppLogin'

export default AppLogin
