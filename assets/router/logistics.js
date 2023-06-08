import AppTablePageSuspense from '../components/pages/table/AppTablePageSuspense.vue'
import {readonly} from 'vue'

export default [
    {
        component: AppTablePageSuspense,
        meta: {title: 'Transporteurs — T-Concept GPAO'},
        name: 'carriers',
        path: '/carriers',
        props: {
            apiBaseRoute: 'carriers',
            fields: [
                {label: 'Nom', name: 'name'},
                {label: 'Adresse', name: 'address.address'},
                {label: 'Complément d\'adresse', name: 'address.address2'},
                {label: 'Ville', name: 'address.city'},
                {label: 'Code postal', name: 'address.zipCode', sort: false},
                {label: 'Pays', name: 'address.country', options: {base: 'countries', value: 'code'}, type: 'select'},
                {label: 'Numéro de téléphone', name: 'address.phoneNumber', sort: false},
                {label: 'E-mail', name: 'address.email'}
            ],
            icon: 'shuttle-van',
            sort: readonly({label: 'Nom', name: 'name'}),
            title: 'Transporteurs'
        }
    },
    {
        component: AppTablePageSuspense,
        meta: {title: 'Incoterms — T-Concept GPAO'},
        name: 'incoterms',
        path: '/incoterms',
        props: {
            apiBaseRoute: 'incoterms',
            fields: [{label: 'Code', name: 'code'}, {label: 'Nom', name: 'name'}],
            icon: 'file-contract',
            sort: readonly({label: 'Code', name: 'code'}),
            title: 'Incoterms'
        }
    }
]
