export default [
    {
        component: () => import('../pages/component/AppcomponentFormShow.vue'),
        meta: {requiresAuth: true},
        name: 'componentFormShow',
        path: '/component/formshow'
    }
]
