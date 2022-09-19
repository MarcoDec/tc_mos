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
                {create: true, label: 'Nom', name: 'name', search: true, sort: true, update: true},
                {create: true, label: 'Début', name: 'start', search: true, sort: true, type: 'time', update: true},
                {create: true, label: 'Début pause', name: 'startBreak', search: true, sort: true, type: 'time', update: true},
                {create: true, label: 'Fin pause', name: 'endBreak', search: true, sort: true, type: 'time', update: true},
                {create: true, label: 'Fin', name: 'end', search: true, sort: true, type: 'time', update: true}
            ],
            icon: 'clock',
            title: 'Plages horaires'
        }
    }
]
