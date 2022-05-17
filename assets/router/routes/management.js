export default [
    {
        component: () => import('../pages/AppCollectionTablePage'),
        meta: {requiresAuth: true},
        name: 'colors',
        path: '/colors',
        props: {icon: 'palette', title: 'Couleurs'}
    }
]
