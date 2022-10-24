import AppTablePageSuspense from '../components/pages/table/AppTablePageSuspense.vue'
import {readonly} from 'vue'

export default [
    {
        component: AppTablePageSuspense,
        meta: {title: 'Formateurs extérieurs — T-Concept GPAO'},
        name: 'out-trainers',
        path: '/out-trainers',
        props: {
            fields: [
                {label: 'Nom', name: 'name'},
                {label: 'Prénom', name: 'surname'},
                {label: 'Adresse', name: 'address.address'},
                {label: 'Complément d\'adresse', name: 'address.address2'},
                {label: 'Ville', name: 'address.city'},
                {label: 'Code postal', name: 'address.zipCode', sort: false},
                {label: 'Pays', name: 'address.country', options: {base: 'countries', value: 'code'}, type: 'select'},
                {label: 'Numéro de téléphone', name: 'address.phoneNumber', sort: false},
                {label: 'E-mail', name: 'address.email'}
            ],
            icon: 'user-graduate',
            sort: readonly({label: 'Nom', name: 'name'}),
            title: 'Formateurs extérieurs'
        }
    }
]
