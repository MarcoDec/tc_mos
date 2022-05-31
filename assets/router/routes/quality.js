export default [
    {
        component: () => import('../pages/AppTablePage'),
        meta: {requiresAuth: true},
        name: 'reject-types',
        path: '/reject-types',
        props: {
            brands: true,
            fields: [{label: 'Nom', name: 'name'}],
            icon: 'elementor',
            title: 'Catégories de rejets de production'
        }
    }
]
