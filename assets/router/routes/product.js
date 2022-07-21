export default [
    {
        component: async () => import('../pages/product/AppProductShow.vue'),
        meta: {requiresAuth: true},
        name: 'productShow',
        path: '/product/show'
    }
]
