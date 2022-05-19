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
                store: () => import('../../stores/management/colors.js'),
                title: 'Couleurs'
            }
        }
    }
]
