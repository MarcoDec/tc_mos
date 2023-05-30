import AppShowGui from '../components/pages/AppShowGui.vue'
import AppTablePageSuspense from '../components/pages/table/AppTablePageSuspense.vue'
import AppTreePageAttribute from '../components/pages/tree/AppTreePageAttribute.vue'
import AppTreePageSuspense from '../components/pages/tree/AppTreePageSuspense.vue'
import {readonly} from 'vue'

export default [
    {
        component: AppTablePageSuspense,
        meta: {title: 'Attributs — T-Concept GPAO'},
        name: 'attributes',
        path: '/attributes',
        props: {
            apiBaseRoute: 'attributes',
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
        component: AppTreePageSuspense,
        meta: {title: 'Familles de composants — T-Concept GPAO'},
        name: 'component-families',
        path: '/component-families',
        props: {
            fields: [
                {label: 'Parent', name: 'parent', options: {existing: 'component-families'}, type: 'select'},
                {label: 'Code', name: 'code'},
                {label: 'Nom', name: 'name'},
                {label: 'Cuivre', name: 'copperable', type: 'boolean'},
                {label: 'Code douanier', name: 'customsCode'},
                {label: 'Icône', name: 'file', type: 'file'}
            ],
            label: 'composants',
            tag: AppTreePageAttribute
        }
    },
    {
        component: AppShowGui,
        meta: {container: false, title: 'Fournisseur — T-Concept GPAO'},
        name: 'supplier',
        path: '/supplier'
    },
    {
        component: AppTablePageSuspense,
        meta: {title: 'Paramètres production— T-Concept GPAO'},
        name: 'purchase parameters',
        path: '/purchase-parameters',
        props: {
            apiBaseRoute: 'parameters',
            disableAdd: true,
            disableRemove: true,
            fields: [
                {label: 'Nom', name: 'name', update: false},
                {label: 'Description', name: 'description', type: 'textarea'},
                {label: 'Type', name: 'kind', type: 'text', update: false},
                {label: 'Valeur', name: 'value', type: 'text'}
            ],
            icon: 'gear',
            readFilter: '?page=1&pagination=false&type=purchase',
            sort: readonly({label: 'Nom', name: 'name'}),
            title: 'Paramètres'
        }
    }
]
