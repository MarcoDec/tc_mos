import AppShowGuiEmployee from '../components/pages/hr/AppShowGuiEmployee.vue'
import AppTablePageSuspense from '../components/pages/table/AppTablePageSuspense.vue'
import {readonly} from 'vue'

export default [
    {
        component: AppTablePageSuspense,
        meta: {title: 'Catégories d\'événements des employés — T-Concept GPAO'},
        name: 'event-types',
        path: '/event-types',
        props: {
            apiBaseRoute: 'event-types',
            brands: true,
            fields: [
                {label: 'Nom', name: 'name'},
                {
                    label: 'Vers le statut',
                    name: 'toStatus',
                    options: {base: 'event-types', value: 'code'},
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
            apiBaseRoute: 'out-trainers',
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
            apiBaseRoute: 'time-slots',
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
        component: AppShowGuiEmployee,
        meta: {container: false, title: 'Employee — T-Concept GPAO'},
        name: 'employee',
        path: '/employee/:id_employee'
    },
    {
        component: AppTablePageSuspense,
        meta: {title: 'Paramètres RH — T-Concept GPAO'},
        name: 'hr parameters',
        path: '/hr-parameters',
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
            readFilter: '?page=1&pagination=false&type=hr',
            sort: readonly({label: 'Nom', name: 'name'}),
            title: 'Paramètres'
        }
    },
    {
        component: AppTablePageSuspense,
        meta: {title: 'Définition des Types de compétence — T-Concept GPAO'},
        name: 'skill-types',
        path: '/skill-types',
        props: {
            apiBaseRoute: 'skill-types',
            fields: [
                {label: 'Nom', name: 'name'}
            ],
            icon: 'signal',
            sort: readonly({label: 'Nom', name: 'name'}),
            title: 'Définition des Types de compétence'
        }
    }
]
