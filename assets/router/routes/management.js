export default [
    {
        component: () => import('../pages/AppTablePage'),
        meta: {requiresAuth: true},
        name: 'colors',
        path: '/colors',
        props: {fields: [{label: 'Nom', name: 'name'}, {label: 'RGB', name: 'rgb'}], icon: 'palette', title: 'Couleurs'}
    }
]
