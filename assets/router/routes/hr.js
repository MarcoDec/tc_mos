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
    },
    {
        component: () => import('../pages/AppSuspenseWrapper'),
        meta: {requiresAuth: true},
        name: 'out-trainers',
        path: '/out-trainers',
        props: {
            component: () => import('../pages/hr/event/AppTablePageEventType.vue'),
            properties: {icon: 'user-graduate', title: 'Formateurs extérieurs'}
        }
    }
]
