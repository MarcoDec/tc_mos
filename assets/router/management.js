import AppTablePageSuspense from '../components/pages/table/AppTablePageSuspense.vue'
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
    }
]
