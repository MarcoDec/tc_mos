export default [
    {
        component: () => import('../pages/component/AppComponentPage.vue'),
        meta: {requiresAuth: true},
        name: 'component-list',
        path: '/component-list'
    }
]
