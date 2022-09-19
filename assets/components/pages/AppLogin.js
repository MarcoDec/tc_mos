import AppForm from '../form/AppForm'
import {h} from 'vue'

const fields = [
    {label: 'Identifiant', name: 'username'},
    {label: 'Mot de passe', name: 'password', type: 'password'}
]

function AppLogin() {
    return h(AppForm, {fields})
}

AppLogin.displayName = 'AppLogin'

export default AppLogin
