import AppShowGui from '../components/pages/AppShowGui.vue'
import AppTablePageSuspense from '../components/pages/table/AppTablePageSuspense.vue'
import {readonly} from 'vue'

export default [
    {
        component: AppTablePageSuspense,
        meta: {title: 'Attributs — T-Concept GPAO'},
        name: 'attributes',
        path: '/attributes',
        props: {
            fields: [
                {label: 'Nom', name: 'name'},
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
                {label: 'Unité', name: 'unit', options: {base: 'units'}, sortName: 'unit.code', type: 'select'},
                {create: false, label: 'Familles', name: 'familiesName', search: false, sort: false, update: false}
            ],
            icon: 'magnet',
            sort: readonly({label: 'Nom', name: 'name'}),
            title: 'Attributs'
        }
    },
    {
        component: AppShowGui,
        meta: {title: 'Fournisseur — T-Concept GPAO'},
        name: 'supplier-show',
        path: '/supplier-show'
    }
]
