import AppTablePageSuspense from '../components/pages/table/AppTablePageSuspense.vue'
import Fields from '../utils/Fields'
import {prepareOptions} from '../stores/option/options'
import {readonly} from 'vue'

const name = {label: 'Nom', name: 'name'}

export default [
    {
        component: AppTablePageSuspense,
        meta: {title: 'Transporteurs — T-Concept GPAO'},
        name: 'carriers',
        path: '/carriers',
        props: {
            fields: Fields.generate([
                name,
                {label: 'Adresse', name: 'address.address'},
                {label: 'Complément d\'adresse', name: 'address.address2'},
                {label: 'Ville', name: 'address.city'},
                {label: 'Code postal', name: 'address.zipCode', sort: false},
                {label: 'Pays', name: 'address.country', options: prepareOptions('countries', 'code'), type: 'select'},
                {label: 'Numéro de téléphone', name: 'address.phoneNumber', sort: false},
                {label: 'E-mail', name: 'address.email'}
            ]),
            icon: 'shuttle-van',
            sort: readonly(name),
            title: 'Transporteurs'
        }
    },
    {
        component: AppTablePageSuspense,
        meta: {title: 'Incoterms — T-Concept GPAO'},
        name: 'incoterms',
        path: '/incoterms',
        props: {
            fields: Fields.generate([{label: 'Code', name: 'code'}, name]),
            icon: 'file-contract',
            sort: readonly(name),
            title: 'Incoterms'
        }
    }
]
