import AppSocity from './pages/direction/AppSocity.vue'
import AppTablePageSuspense from '../components/pages/table/AppTablePageSuspense.vue'
import MonthCalendar from '../router/pages/company/agenda/agendaMonth/MonthCalendar.vue'
import {readonly} from 'vue'

export default [
    {
        component: AppTablePageSuspense,
        meta: {title: 'Couleurs — T-Concept GPAO'},
        name: 'colors',
        path: '/colors',
        props: {
            fields: [{label: 'Nom', name: 'name'}, {label: 'RGB', name: 'rgb', type: 'color'}],
            icon: 'palette',
            sort: readonly({label: 'Nom', name: 'name'}),
            title: 'Couleurs'
        }
    },
    {
        component: AppTablePageSuspense,
        meta: {title: 'Délais de paiement des factures — T-Concept GPAO'},
        name: 'invoice-time-dues',
        path: '/invoice-time-dues',
        props: {
            fields: [
                {label: 'Nom', name: 'name'},
                {label: 'Jours', name: 'days', sort: false, type: 'number'},
                {label: 'Fin du mois', name: 'endOfMonth', sort: false, type: 'boolean'},
                {label: 'Jours après la fin du mois', name: 'daysAfterEndOfMonth', sort: false, type: 'number'}
            ],
            icon: 'hourglass-half',
            sort: readonly({label: 'Nom', name: 'name'}),
            title: 'Délais de paiement des factures'
        }
    },
    {
        component: AppTablePageSuspense,
        meta: {title: 'Imprimantes — T-Concept GPAO'},
        name: 'printers',
        path: '/printers',
        props: {
            disableRemove: true,
            fields: [
                {create: false, label: 'Nom', name: 'name', search: false, sort: false, update: false},
                {create: false, label: 'IP', name: 'ip', search: false, sort: false, update: false},
                {
                    create: false,
                    hideLabelValue: true,
                    label: 'Couleur',
                    name: 'color',
                    options: [{text: '#00cc00', value: 'green'}, {text: '#ffff33', value: 'yellow'}],
                    search: false,
                    sort: false,
                    type: 'color',
                    update: false
                },
                {create: false, label: 'Compagnie', name: 'company.name', search: false, sort: false, update: false}
            ],
            icon: 'print',
            sort: readonly({label: 'Nom', name: 'name'}),
            title: 'Imprimantes'
        }
    },
    {
        component: AppTablePageSuspense,
        meta: {title: 'Unités — T-Concept GPAO'},
        name: 'units',
        path: '/units',
        props: {
            fields: [
                {label: 'Code', name: 'code'},
                {label: 'Nom', name: 'name'},
                {label: 'Base', name: 'base', type: 'number'},
                {label: 'Parent', name: 'parent', options: {base: 'units'}, sortName: 'parent.code', type: 'select'}
            ],
            icon: 'ruler-horizontal',
            sort: readonly({label: 'Nom', name: 'name'}),
            title: 'Unités'
        }
    },
    {
        component: AppTablePageSuspense,
        meta: {title: 'Messages TVA — T-Concept GPAO'},
        name: 'vat-messages',
        path: '/vat-messages',
        props: {
            fields: [{label: 'Nom', name: 'name'}],
            icon: 'comments-dollar',
            sort: readonly({label: 'Nom', name: 'name'}),
            title: 'Messages TVA'
        }
    },
    {
        component: MonthCalendar,
        meta: {container: false, title: 'Agenda — T-Concept GPAO'},
        name: 'agenda',
        path: '/agenda'
    },
    {
        component: AppSocity,
        meta: {title: 'Listes des sociétés  — T-Concept GPAO'},
        name: 'society-list',
        path: '/society-list',
        props: {
            fields: [
                {label: 'Nom', min: true, name: 'name', trie: true, type: 'text'},
                {label: 'Adresse', min: false, name: 'address', trie: true, type: 'text'},
                {label: 'Complément d\'adresse', min: false, name: 'address2', trie: true, type: 'text'},
                {label: 'Ville', min: true, name: 'city', trie: true, type: 'text'},
                {label: 'Pays', min: true, name: 'country', trie: true, type: 'text'},
                {label: 'Email', min: false, name: 'email', trie: true, type: 'text'},
                {label: 'Phone Number', min: false, name: 'phoneNumber', trie: true, type: 'text'},
                {label: 'Zip Code', min: false, name: 'zipCode', trie: true, type: 'text'}
            ],
            icon: 'city',
            title: 'Société'
        }
    }
]
