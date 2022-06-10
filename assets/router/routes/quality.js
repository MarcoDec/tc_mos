export default [
    {
        component: () => import('../pages/AppTablePage'),
        meta: {requiresAuth: true},
        name: 'reject-types',
        path: '/reject-types',
        props: {
            brands: true,
            fields: [{label: 'Nom', name: 'name', sort: true}],
            icon: 'elementor',
            title: 'Catégories de rejets de production'
        }
    },
    {
        component: () => import('../pages/AppTablePage'),
        meta: {requiresAuth: true},
        name: 'quality-types',
        path: '/quality-types',
        props: {
            brands: true,
            fields: [{label: 'Nom', name: 'name', sort: true}],
            icon: 'elementor',
            title: 'Critères qualités'
        }
    }
]
