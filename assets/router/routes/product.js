export default [
    {
        component: async () => import('../pages/product/AppProductShowInlist.vue'),
        meta: {requiresAuth: true},
        name: 'productShow',
        path: '/product/show/:id_product'
    }
]
