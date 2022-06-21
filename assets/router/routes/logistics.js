import AppTablePage from '../pages/AppTablePage'

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
        component: AppTablePage,
        meta: {requiresAuth: true},
        name: 'incoterms',
        path: '/incoterms',
        props: {
            fields: [
                {label: 'Code', name: 'code', search: true, sort: true, update: true},
                {label: 'Nom', name: 'name', search: true, sort: true, update: true}
            ],
            icon: 'file-contract',
            title: 'Incoterms'
        }
    }
]
