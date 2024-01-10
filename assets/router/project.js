import AppShowGuiProduct from '../components/pages/project/product/AppShowGuiProduct.vue'
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
            apiBaseRoute: 'project-operations',
            fields: [
                {label: 'Code', name: 'code'},
                {label: 'Nom', name: 'name'},
                {label: 'Auto', name: 'auto', sort: false, type: 'boolean'},
                {label: 'Limite', name: 'boundary', sort: false},
                {label: 'Cadence', measure: {code: 'U/jr', value: 'valeur'}, name: 'cadence', search: false, sort: false, type: 'measure'},
                {label: 'Prix', name: 'price', search: false, sort: false, type: 'price'},
                {label: 'Durée', measure: {code: 'h', value: 'valeur'}, name: 'time', search: false, sort: false, type: 'measure'},
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
            apiBaseRoute: 'operation-types',
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
    },
    {
        component: AppShowGuiProduct,
        meta: {container: false, title: 'Produit — T-Concept GPAO'},
        name: 'product',
        path: '/product/:id_product'
    },
    {
        component: AppTablePageSuspense,
        meta: {title: 'Paramètres production— T-Concept GPAO'},
        name: 'project parameters',
        path: '/project-parameters',
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
            readFilter: '?page=1&pagination=false&type=project',
            sort: readonly({label: 'Nom', name: 'name'}),
            title: 'Paramètres'
        }
    }
]
