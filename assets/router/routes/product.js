export default [
    {
        component: () => import('../pages/product/AppProductFormShow.vue'),
        meta: {requiresAuth: true},
        name: 'productFormShow',
        path: '/product/formshow'
    }
]
