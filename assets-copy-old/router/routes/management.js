import AppTablePage from '../pages/AppTablePage'

export default [
    {
        component: () => import('../pages/management/AppTablePageColor'),
        meta: {requiresAuth: true},
        name: 'colors',
        path: '/colors',
        props: {
            fields: [
                {label: 'Nom', name: 'name', sort: true, update: true},
                {label: 'RGB', name: 'rgb', sort: true, type: 'color', update: true}
            ],
            icon: 'palette',
            title: 'Couleurs'
        }
    },
    {
        component: async () => import('../pages/management/AppCurrencies.vue'),
        meta: {requiresAuth: true},
        name: 'currencies',
        path: '/currencies'
    },
    {
        component: AppTablePage,
        meta: {requiresAuth: true},
        name: 'invoice-time-dues',
        path: '/invoice-time-dues',
        props: {
            fields: [
                {label: 'Nom', name: 'name', sort: true, update: true},
                {label: 'Jours', name: 'days', sort: false, type: 'number', update: true},
                {label: 'Fin du mois', name: 'endOfMonth', sort: false, type: 'boolean', update: true},
                {
                    label: 'Jours après la fin du mois',
                    name: 'daysAfterEndOfMonth',
                    sort: false,
                    type: 'number',
                    update: true
                }
            ],
            icon: 'hourglass-half',
            title: 'Délais de paiement des factures'
        }
    },
    {
        component: () => import('../pages/AppSuspenseWrapper'),
        meta: {requiresAuth: true},
        name: 'units',
        path: '/units',
        props: {
            component: () => import('../pages/management/AppTablePageUnit.vue'),
            properties: {icon: 'ruler-horizontal', title: 'Unités'}
        }
    },
    {
        component: AppTablePage,
        meta: {requiresAuth: true},
        name: 'vat-messages',
        path: '/vat-messages',
        props: {
            fields: [{label: 'Nom', name: 'name', sort: true, update: true}],
            icon: 'comments-dollar',
            title: 'Messages TVA'
        }
    }
]
