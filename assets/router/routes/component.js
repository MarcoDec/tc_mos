export default [
    {
        component: async () => import('../pages/component/AppcomponentShowInlist.vue'),
        meta: {requiresAuth: true},
        name: 'componentShow',
        path: '/component/show'
    }
]
