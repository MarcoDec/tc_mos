export default [
    {
        component: () => import('../pages/product/AppProductPage.vue'),
        meta: {requiresAuth: true},
        name: 'product-list',
        path: '/product-list'
    }
]
