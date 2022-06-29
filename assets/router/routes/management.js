import AppTablePage from '../pages/AppTablePage'

export default [
    {
        component: () => import('../pages/management/AppTablePageColor'),
        meta: {requiresAuth: true},
        name: 'colors',
        path: '/colors',
        props: {
            fields: [
                {create: true, label: 'Nom', name: 'name', search: true, sort: true, update: true},
                {create: true, label: 'RGB', name: 'rgb', search: true, sort: true, type: 'color', update: true}
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
                {create: true, label: 'Nom', name: 'name', search: true, sort: true, update: true},
                {create: true, label: 'Jours', name: 'days', search: true, sort: false, type: 'number', update: true},
                {create: true, label: 'Fin du mois', name: 'endOfMonth', search: true, sort: false, type: 'boolean', update: true},
                {create: true, label: 'Jours après la fin du mois', name: 'daysAfterEndOfMonth', search: true, sort: false, type: 'number', update: true}
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
            fields: [{create: true, label: 'Nom', name: 'name', search: true, sort: true, update: true}],
            icon: 'comments-dollar',
            title: 'Messages TVA'
        }
    }
]
