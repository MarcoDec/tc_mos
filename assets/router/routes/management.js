export default [
    {
        component: () => import('../pages/management/AppTablePageColor'),
        meta: {requiresAuth: true},
        name: 'colors',
        path: '/colors',
        props: {
            fields: [{label: 'Nom', name: 'name'}, {label: 'RGB', name: 'rgb', type: 'color'}],
            icon: 'palette',
            title: 'Couleurs'
        }
    },
    {
        component: () => import('../pages/AppTablePage'),
        meta: {requiresAuth: true},
        name: 'invoice-time-dues',
        path: '/invoice-time-dues',
        props: {
            fields: [
                {label: 'Nom', name: 'name'},
                {label: 'Jours', name: 'days', type: 'number'},
                {label: 'Fin du mois', name: 'endOfMonth', type: 'boolean'},
                {label: 'Jours après la fin du mois', name: 'daysAfterEndOfMonth', type: 'number'}
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
        component: () => import('../pages/AppTablePage'),
        meta: {requiresAuth: true},
        name: 'vat-messages',
        path: '/vat-messages',
        props: {
            fields: [{label: 'Nom', name: 'name'}],
            icon: 'comments-dollar',
            title: 'Messages TVA'
        }
    }
]
