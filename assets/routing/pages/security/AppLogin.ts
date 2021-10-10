import {h, resolveComponent} from 'vue'
import {Field} from '../../../store/bootstrap-5/Field'
import type {VNode} from '@vue/runtime-core'

export default function AppLogin(): VNode {
    return h(
        resolveComponent('AppRow'),
        () => h(
            resolveComponent('AppCard'),
            {class: 'bg-blue col'},
            () => h(resolveComponent('AppForm'), {
                fields: [
                    new Field('Identifiant', 'username'),
                    new Field('Mot de passe', 'password', 'password')
                ],
                id: 'login'
            })
        )
    )
}
