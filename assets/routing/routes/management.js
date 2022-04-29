import {ColorRepository, InvoiceTimeDueRepository, UnitRepository, VatMessageRepository} from '../../store/modules'

export default [
    {
        component: async () => import('../pages/AppCollectionTablePage.vue'),
        meta: {requiresAuth: true},
        name: 'colors',
        path: '/colors',
        props: {
            fields: [
                {
                    create: true,
                    filter: true,
                    label: 'Nom',
                    name: 'name',
                    sort: true,
                    type: 'text',
                    update: true
                },
                {
                    create: true,
                    filter: true,
                    label: 'RGB',
                    name: 'rgb',
                    sort: true,
                    type: 'color',
                    update: true
                }
            ],
            icon: 'palette',
            repo: ColorRepository,
            role: 'isManagementAdmin',
            title: 'Couleurs'
        }
    },
    {
        component: async () => import('../pages/management/AppCurrency.vue'),
        meta: {requiresAuth: true},
        name: 'currencies',
        path: '/currencies'
    },
    {
        component: async () => import('../pages/AppCollectionTablePage.vue'),
        meta: {requiresAuth: true},
        name: 'invoice-time-dues',
        path: '/invoice-time-dues',
        props: {
            fields: [
                {
                    create: true,
                    filter: true,
                    label: 'Nom',
                    name: 'name',
                    sort: true,
                    type: 'text',
                    update: true
                },
                {
                    create: true,
                    filter: true,
                    label: 'Jours',
                    name: 'days',
                    sort: false,
                    type: 'number',
                    update: true
                },
                {
                    create: true,
                    filter: true,
                    label: 'Fin du mois',
                    name: 'endOfMonth',
                    sort: false,
                    type: 'boolean',
                    update: true
                },
                {
                    create: true,
                    filter: true,
                    label: 'Jours après la fin du mois',
                    name: 'daysAfterEndOfMonth',
                    sort: false,
                    type: 'number',
                    update: true
                }
            ],
            icon: 'hourglass-half',
            repo: InvoiceTimeDueRepository,
            role: 'isManagementAdmin',
            title: 'Délais de paiement des factures'
        }
    },
    {
        component: async () => import('../pages/AppCollectionTablePage.vue'),
        meta: {requiresAuth: true},
        name: 'units',
        path: '/units',
        props: {
            fields: [
                {
                    create: true,
                    filter: true,
                    label: 'Code',
                    name: 'code',
                    sort: true,
                    type: 'text',
                    update: true
                },
                {
                    create: true,
                    filter: true,
                    label: 'Nom',
                    name: 'name',
                    sort: true,
                    type: 'text',
                    update: true
                },
                {
                    create: true,
                    filter: true,
                    label: 'Base',
                    name: 'base',
                    sort: true,
                    type: 'number',
                    update: true
                },
                {
                    create: true,
                    filter: true,
                    label: 'Parent',
                    name: 'parent',
                    repo: UnitRepository,
                    sort: true,
                    sortName: 'parent.code',
                    type: 'select',
                    update: true
                }
            ],
            icon: 'ruler-horizontal',
            repo: UnitRepository,
            role: 'isManagementAdmin',
            title: 'Unités'
        }
    },
    {
        component: async () => import('../pages/AppCollectionTablePage.vue'),
        meta: {requiresAuth: true},
        name: 'vat-messages',
        path: '/vat-messages',
        props: {
            fields: [{
                create: true,
                filter: true,
                label: 'Message',
                name: 'name',
                sort: true,
                type: 'text',
                update: true
            }],
            icon: 'comments-dollar',
            repo: VatMessageRepository,
            role: 'isManagementAdmin',
            title: 'Messages TVA'
        }
    }
]
