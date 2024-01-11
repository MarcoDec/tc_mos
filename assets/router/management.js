import AppCompanyList from '../components/pages/management/company/AppCompanyList.vue'
import AppBalanceSheetShow from '../components/pages/management/company/balance_sheet/AppBalanceSheetShow.vue'
import AppSocietyList from '../components/pages/management/society/bottom/AppSocietyList.vue'
import AppTablePageSuspense from '../components/pages/table/AppTablePageSuspense.vue'
import MonthCalendar from '../components/pages/management/agenda/agendaMonth/MonthCalendar.vue'
import {readonly} from 'vue'
import AppShowGuiCompany from '../components/pages/management/company/AppShowGuiCompany.vue'

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
                {create: true, label: 'Nom', name: 'name', search: true, sort: true, update: true},
                {create: true, label: 'IP', name: 'ip', search: true, sort: true, update: true},
                {
                    create: true,
                    hideLabelValue: true,
                    label: 'Couleur',
                    name: 'color',
                    options: [{text: '#00cc00', value: 'green'}, {text: '#ffff33', value: 'yellow'}],
                    search: false,
                    sort: true,
                    type: 'color',
                    update: true
                },
                {
                    create: true,
                    label: 'Compagnie',
                    name: 'company',
                    options: {base: 'companies'},
                    search: true,
                    sort: false,
                    type: 'select',
                    update: true
                },
                {
                    create: true,
                    label: 'Largeur Max Etiquette',
                    name: 'maxLabelWidth',
                    search: true,
                    sort: true,
                    step: 0.1,
                    type: 'number',
                    update: true
                },
                {
                    create: true,
                    label: 'Hauteur Max Etiquette',
                    name: 'maxLabelHeight',
                    search: true,
                    sort: true,
                    step: 0.1,
                    type: 'number',
                    update: true
                }
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
        component: AppCompanyList,
        meta: {title: 'Listes des compagnies  — T-Concept GPAO'},
        name: 'company-list',
        path: '/company-list',
        props: {
            icon: 'city',
            title: 'Compagnies'
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
        path: '/company/:id_company',
        props: {
            icon: 'city',
            title: 'Compagnie'
        }
    },
    {
        component: AppTablePageSuspense,
        meta: {title: 'Suivi des Dépenses et Ventes — T-Concept GPAO'},
        name: 'suivi_depenses_ventes',
        path: '/suivi-depenses-ventes',
        props: {
            apiBaseRoute: 'balance-sheets',
            fields: [
                {label: 'Mois', name: 'month', type: 'number', step: 1, sort: true},
                {label: 'Année', name: 'year', type: 'number', step: 1, sort: true},
                {label: 'Company', name: 'company', options: {base: 'companies'}, sort: false, /* sortName: 'company.name',  */type: 'select'},
                {label: 'Devise', name: 'currency', options: {base: 'currencies'}, sort: false, /* sortName: 'currency.name', */ type: 'select'},
                {create: false, label: 'Dépenses totales', name: 'totalExpense', sort: false, type: 'price', update: false},
                {create: false, label: 'Ventes totales', name: 'totalIncome', sort: false, type: 'price', update: false}
            ],
            enableShow: true,
            showRouteName: 'suivi_depenses_ventes_show',
            icon: 'gauge-high',
            sort: readonly({label: 'Nom', name: 'name'}),
            title: 'Suivi des Dépenses et Ventes'
        }
    },
    {
        component: AppBalanceSheetShow,
        meta: {title: 'Détails des Dépenses et Ventes — T-Concept GPAO'},
        name: 'suivi_depenses_ventes_show',
        path: '/suivi-depenses-ventes/show/:id'
    },
    {
        component: AppTablePageSuspense,
        meta: {title: 'Gestion des devises'},
        name: 'currencies',
        path: '/currencies',
        props: {
            apiBaseRoute: 'currencies',
            disableAdd: true,
            disableRemove: true,
            fields: [
                { label: 'Nom', name: 'code', create: true, update: false, search: true, sort: true },
                { label: 'Description', name: 'name', create: true, update: false, search: true, sort: true },
                { label: 'Symbol', name: 'symbol', create: true, update: false, search: true, sort: true },
                { label: 'Activé/Desactivé', name: 'active', type: 'boolean', create: true, update: true, search: true, sort: false },
                { label: 'Devise parente', name: 'parent', type: 'select', options: { base: 'currencies' }, create: true, update: false, search: true, sort: false },
                { label: 'Ratio / parent', name: 'base', type: 'number', step: 0.01, create: true, update: false, search: true, sort: true }
            ],
            icon: 'comments-dollar',
            sort: readonly({label: 'Nom', name: 'name'}),
            title: 'Gestion des devises'
        }
    },
]
