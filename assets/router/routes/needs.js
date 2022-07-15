export default [
    {
        component: () => import('../pages/needs/AppNeedsPage.vue'),
        meta: {requiresAuth: true},
        name: 'needs',
        path: '/needs'
    }
]
