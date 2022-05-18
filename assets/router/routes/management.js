export default [
    {
        component: () => import('../pages/AppSuspenseWrapper'),
        meta: {requiresAuth: true},
        name: 'colors',
        path: '/colors',
        props: {
            component: () => import('../pages/AppTablePage.vue'),
            properties: {
                fields: [{label: 'Nom', name: 'name'}, {label: 'RGB', name: 'rgb'}],
                icon: 'palette',
                store: () => import('../../stores/management/colors.js'),
                title: 'Couleurs'
            }
        }
    }
]
