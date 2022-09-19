export default [
    {
        component: () => import('../pages/production/engine/AppTablePageEngineGroup.vue'),
        meta: {requiresAuth: true},
        name: 'engine-groups',
        path: '/engine-groups',
        props: {icon: 'wrench', title: 'Groupes d\'équipements'}
    },
    {
        component: () => import('../pages/AppSuspenseWrapper'),
        meta: {requiresAuth: true},
        name: 'manufacturers',
        path: '/manufacturers',
        props: {
            component: () => import('../pages/production/engine/AppTablePageManufacturer.vue'),
            properties: {icon: 'oil-well', title: 'Fabricants'}
        }
    },
    {
        component: () => import('../pages/AppSuspenseWrapper'),
        meta: {requiresAuth: true},
        name: 'zones',
        path: '/zones',
        props: {
            component: () => import('../pages/production/company/AppTablePageZone.vue'),
            properties: {icon: 'map-marked', title: 'Zones'}
        }
    }
]
