import AppTablePage from '../pages/AppTablePage'

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
        component: AppTablePage,
        meta: {requiresAuth: true},
        name: 'time-slots',
        path: '/time-slots',
        props: {
            fields: [
                {label: 'Nom', name: 'name', sort: true, update: true},
                {label: 'Début', name: 'start', sort: true, type: 'time', update: true},
                {label: 'Début pause', name: 'startBreak', sort: true, type: 'time', update: true},
                {label: 'Fin pause', name: 'endBreak', sort: true, type: 'time', update: true},
                {label: 'Fin', name: 'end', sort: true, type: 'time', update: true}
            ],
            icon: 'clock',
            title: 'Plages horaires'
        }
    }
]
