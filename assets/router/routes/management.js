export default [
    {
        component: () => import('../pages/AppSuspenseWrapper'),
        meta: {requiresAuth: true},
        name: 'colors',
        path: '/colors',
        props: {
            component: () => import('../pages/management/AppTablePageColor'),
            properties: {
                fields: [{label: 'Nom', name: 'name'}, {label: 'RGB', name: 'rgb', type: 'color'}],
                icon: 'palette',
                store: () => import('../../stores/management/colors'),
                title: 'Couleurs'
            }
        }
    },
    {
        component: () => import('../pages/AppSuspenseWrapper'),
        meta: {requiresAuth: true},
        name: 'invoice-time-dues',
        path: '/invoice-time-dues',
        props: {
            component: () => import('../pages/AppTablePage'),
            properties: {
                fields: [
                    {label: 'Nom', name: 'name'},
                    {label: 'Jours', name: 'days', type: 'number'},
                    {label: 'Fin du mois', name: 'endOfMonth', type: 'boolean'},
                    {label: 'Jours après la fin du mois', name: 'daysAfterEndOfMonth', type: 'number'}
                ],
                icon: 'hourglass-half',
                store: () => import('../../stores/management/invoiceTimeDues'),
                title: 'Délais de paiement des factures'
            }
        }
    },
    {
        component: () => import('../pages/AppSuspenseWrapper'),
        meta: {requiresAuth: true},
        name: 'vat-messages',
        path: '/vat-messages',
        props: {
            component: () => import('../pages/AppTablePage'),
            properties: {
                fields: [{label: 'Nom', name: 'name'}],
                icon: 'comments-dollar',
                store: () => import('../../stores/management/vatMessages'),
                title: 'Messages TVA'
            }
        }
    }
]
