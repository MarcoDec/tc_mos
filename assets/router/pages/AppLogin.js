import AppCard from '../../components/AppCard'
import AppForm from '../../components/form/AppForm'
import {h} from 'vue'
import {useRoute} from 'vue-router'

function AppLogin() {
    const route = useRoute()
    return h(
        'div',
        {class: 'row', id: route.name},
        h(AppCard, {class: 'col', title: 'Connexion'}, () => h(AppForm, {
            fields: [
                {label: 'Identifiant', name: 'username'},
                {label: 'Mot de passe', name: 'password', type: 'password'}
            ],
            id: `${route.name}-form`
        }))
    )
}

AppLogin.displayName = 'AppLogin'

export default AppLogin
