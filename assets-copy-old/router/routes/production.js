export default [
    {
        component: () => import('../pages/production/engine/AppTablePageEngineGroup.vue'),
        meta: {requiresAuth: true},
        name: 'engine-groups',
        path: '/engine-groups',
        props: {icon: 'wrench', title: 'Groupes d\'équipements'}
    }
]
