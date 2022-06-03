export default [
    {
        component: () => import('../pages/AppSuspenseWrapper'),
        meta: {requiresAuth: true},
        name: 'carriers',
        path: '/carriers',
        props: {
            component: () => import('../pages/logistics/AppTablePageCarrier.vue'),
            properties: {icon: 'shuttle-van', title: 'Transporteurs'}
        }
    },
    {
        component: () => import('../pages/AppTablePage'),
        meta: {requiresAuth: true},
        name: 'incoterms',
        path: '/incoterms',
        props: {
            fields: [{label: 'Code', name: 'code'}, {label: 'Nom', name: 'name'}],
            icon: 'file-contract',
            title: 'Incoterms'
        }
    }
]
