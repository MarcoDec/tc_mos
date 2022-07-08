export default [
    {
        component: () => import('../pages/manufacturingOrder/AppManufacturingOrderPage.vue'),
        meta: {requiresAuth: true},
        name: 'manufacturingOrder-list',
        path: '/manufacturingOrder-list'
    }
]
