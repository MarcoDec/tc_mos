export default [
    {
        component: () => import('../pages/component/AppComponentFormShow.vue'),
        meta: {requiresAuth: true},
        name: 'componentFormShow',
        path: '/component/formshow'
    }
]
