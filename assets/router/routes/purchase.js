export default [
    {
        component: () => import('../pages/AppTreePage.vue'),
        meta: {requiresAuth: true},
        name: 'component-families',
        path: '/component-families'
    }
]
