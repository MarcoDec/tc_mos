import AppSocietyList from '../components/pages/Management/Society/bottom/AppSocietyList.vue'
import AppTablePageSuspense from '../components/pages/table/AppTablePageSuspense.vue'
import MonthCalendar from '../components/pages/management/company/agenda/agendaMonth/MonthCalendar.vue'
import {readonly} from 'vue'
import AppShowGuiCompany from '../components/pages/Management/Company/AppShowGuiCompany.vue'

export default [
    {
        component: AppTablePageSuspense,
        meta: {title: 'Couleurs — T-Concept GPAO'},
        name: 'colors',
        path: '/colors',
        props: {
            apiBaseRoute: 'colors',
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
            apiBaseRoute: 'invoice-time-dues',
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
            apiBaseRoute: 'printers',
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
            apiBaseRoute: 'units',
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
        meta: {title: 'Supp — T-Concept GPAO'},
        name: 'supplier-contacts',
        path: '/supplier-contacts',
        props: {
            apiBaseRoute: 'supplier-contacts',
            fields: [
                {label: 'Nom', name: 'name'}

            ],
            icon: 'ruler-horizontal',
            sort: readonly({label: 'Nom', name: 'name'}),
            title: 'Supp'
        }
    },
    {
        component: AppTablePageSuspense,
        meta: {title: 'Messages TVA — T-Concept GPAO'},
        name: 'vat-messages',
        path: '/vat-messages',
        props: {
            apiBaseRoute: 'vat-messages',
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
        component: AppSocietyList,
        meta: {title: 'Listes des sociétés  — T-Concept GPAO'},
        name: 'society-list',
        path: '/society-list',
        props: {
            icon: 'city',
            title: 'Société'
        }
    },
    {
        component: AppTablePageSuspense,
        meta: {title: 'Définition des Equipes — T-Concept GPAO'},
        name: 'teams',
        path: '/teams',
        props: {
            apiBaseRoute: 'teams',
            fields: [
                {label: 'Nom', name: 'name'},
                {label: 'Compagnie', name: 'company', options: {base: 'companies'}, sort: false, /* sortName: 'company.name',  */type: 'select'},
                {label: 'Créneaux horaires', name: 'timeSlot', options: {base: 'time-slots'}, sort: false, /* sortName: 'timeSlot.name', */ type: 'select'}
            ],
            icon: 'people-group',
            sort: readonly({label: 'Nom', name: 'name'}),
            title: 'Définition des équipes'
        }
    },
    {
        component: AppShowGuiCompany,
        meta: {container: false, title: 'Entreprise — T-Concept GPAO'},
        name: 'company',
        path: '/company/:id_company'
    }
]
