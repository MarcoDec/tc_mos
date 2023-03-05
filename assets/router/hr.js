import AppTablePageSuspense from '../components/pages/table/AppTablePageSuspense.vue'
import {readonly} from 'vue'

export default [
    {
        component: AppTablePageSuspense,
        meta: {title: 'Catégories d\'événements des employés — T-Concept GPAO'},
        name: 'event-types',
        path: '/event-types',
        props: {
            brands: true,
            fields: [
                {label: 'Nom', name: 'name'},
                {
                    label: 'Vers le statut',
                    name: 'toStatus',
                    options: {base: 'employee-states', value: 'id'},
                    sort: false,
                    type: 'select'
                }
            ],
            icon: 'elementor',
            sort: readonly({label: 'Nom', name: 'name'}),
            title: 'Catégories d\'événements des employés'
        }
    },
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
    },
    {
        component: AppTablePageSuspense,
        meta: {title: 'Plages horaires — T-Concept GPAO'},
        name: 'time-slots',
        path: '/time-slots',
        props: {
            fields: [
                {label: 'Nom', name: 'name'},
                {label: 'Début', name: 'start', type: 'time'},
                {label: 'Début pause', name: 'startBreak', type: 'time'},
                {label: 'Fin pause', name: 'endBreak', type: 'time'},
                {label: 'Fin', name: 'end', type: 'time'}
            ],
            icon: 'clock',
            sort: readonly({label: 'Nom', name: 'name'}),
            title: 'Plages horaires'
        }
    },
    {
        component: () => import('./pages/employee/AppEmployeePage.vue'),
        meta: {requiresAuth: true},
        name: 'employee-list',
        path: '/employee-list'
    }
]
