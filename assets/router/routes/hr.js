export default [
    {
        component: () => import('../pages/AppTablePage'),
        meta: {requiresAuth: true},
        name: 'event-types',
        path: '/event-types',
        props: {
            brands: true,
            fields: [{label: 'Nom', name: 'name'}],
            icon: 'elementor',
            title: 'Catégories d\'événements des employés'
        }
    }
]
