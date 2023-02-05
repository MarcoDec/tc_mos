import AppTablePageSuspense from '../components/pages/table/AppTablePageSuspense.vue'
import AppTreePageSuspense from '../components/pages/tree/AppTreePageSuspense.vue'
import {readonly} from 'vue'

export default [
    {
        component: AppTreePageSuspense,
        meta: {title: 'Familles de produits — T-Concept GPAO'},
        name: 'product-families',
        path: '/product-families',
        props: {
            fields: [
                {label: 'Parent', name: 'parent', options: {existing: 'product-families'}, type: 'select'},
                {label: 'Nom', name: 'name'},
                {label: 'Code douanier', name: 'customsCode'},
                {label: 'Icône', name: 'file', type: 'file'}
            ],
            label: 'produits'
        }
    },
    {
        component: AppTablePageSuspense,
        meta: {title: 'Opérations — T-Concept GPAO'},
        name: 'project-operations',
        path: '/project-operations',
        props: {
            fields: [
                {label: 'Code', name: 'code'},
                {label: 'Nom', name: 'name'},
                {label: 'Auto', name: 'auto', sort: false, type: 'boolean'},
                {label: 'Limite', name: 'boundary', sort: false},
                {label: 'Cadence', name: 'cadence', search: false, sort: false, type: 'measure'},
                {label: 'Prix', name: 'price', search: false, sort: false, type: 'price'},
                {label: 'Durée', name: 'time', search: false, sort: false, type: 'measure'},
                {label: 'Type', name: 'type', options: {base: 'operation-types'}, sortName: 'type.name', type: 'select'}
            ],
            icon: 'atom',
            sort: readonly({label: 'Code', name: 'code'}),
            title: 'Opérations'
        }
    },
    {
        component: AppTablePageSuspense,
        meta: {title: 'Types d\'opérations — T-Concept GPAO'},
        name: 'operation-types',
        path: '/operation-types',
        props: {
            brands: true,
            fields: [
                {label: 'Nom', name: 'name'},
                {label: 'Assemblage', name: 'assembly', sort: false, type: 'boolean'},
                {
                    label: 'Familles',
                    name: 'families',
                    options: {base: 'component-families'},
                    search: false,
                    sort: false,
                    type: 'multiselect'
                }
            ],
            icon: 'elementor',
            sort: readonly({label: 'Nom', name: 'name'}),
            title: 'Types d\'opérations'
        }
    }
]