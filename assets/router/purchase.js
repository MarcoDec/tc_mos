import AppTablePageSuspense from '../components/pages/table/AppTablePageSuspense.vue'
import Fields from '../utils/Fields'
import {prepareOptions} from '../stores/option/options'
import {readonly} from 'vue'

const name = {label: 'Nom', name: 'name'}

export default [
    {
        component: AppTablePageSuspense,
        meta: {title: 'Attributs — T-Concept GPAO'},
        name: 'attributes',
        path: '/attributes',
        props: {
            fields: Fields.generate([
                name,
                {label: 'Description', name: 'description'},
                {
                    label: 'Type',
                    name: 'type',
                    options: [
                        {text: 'Booléen', value: 'bool'},
                        {text: 'Couleur', value: 'color'},
                        {text: 'Entier', value: 'int'},
                        {text: 'Pourcentage', value: 'percent'},
                        {text: 'Texte', value: 'text'},
                        {text: 'Unité', value: 'unit'}
                    ],
                    type: 'select',
                    update: false
                },
                {
                    label: 'Unité',
                    name: 'unit',
                    options: prepareOptions('units'),
                    sortName: 'unit.code',
                    type: 'select'
                },
                {create: false, label: 'Familles', name: 'familiesName', search: false, sort: false, update: false}
            ]),
            icon: 'magnet',
            sort: readonly(name),
            title: 'Attributs'
        }
    }
]
