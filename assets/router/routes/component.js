export default [
    {
        component: async () => import('../pages/component/AppcomponentShow.vue'),
        meta: {requiresAuth: true},
        name: 'componentShow',
        path: '/component/show'
    }
]
