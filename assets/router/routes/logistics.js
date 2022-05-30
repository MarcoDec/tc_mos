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
    }
]
