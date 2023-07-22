export default [
    {
        component: () => import('../../components/pages/purchase/component/AppComponentFormShow.vue'),
        meta: {requiresAuth: true},
        name: 'componentFormShow',
        path: '/component/formshow'
    }
]
