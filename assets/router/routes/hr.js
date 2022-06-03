export default [
    {
        component: () => import('../pages/AppSuspenseWrapper'),
        meta: {requiresAuth: true},
        name: 'event-types',
        path: '/event-types',
        props: {
            component: () => import('../pages/hr/event/AppTablePageEventType.vue'),
            properties: {brands: true, icon: 'elementor', title: 'Catégories d\'événements des employés'}
        }
    },
    {
        component: () => import('../pages/AppSuspenseWrapper'),
        meta: {requiresAuth: true},
        name: 'out-trainers',
        path: '/out-trainers',
        props: {
            component: () => import('../pages/hr/AppTablePageOutTrainer.vue'),
            properties: {icon: 'user-graduate', title: 'Formateurs extérieurs'}
        }
    },
    {
        component: () => import('../pages/AppTablePage'),
        meta: {requiresAuth: true},
        name: 'time-slots',
        path: '/time-slots',
        props: {
            fields: [
                {label: 'Nom', name: 'name'},
                {label: 'Début', name: 'start', type: 'time'},
                {label: 'Début pause', name: 'startBreak', type: 'time'},
                {label: 'Fin pause', name: 'endBreak', type: 'time'},
                {label: 'Fin', name: 'end', type: 'time'}
            ],
            icon: 'clock',
            title: 'Plages horaires'
        }
    }
]
