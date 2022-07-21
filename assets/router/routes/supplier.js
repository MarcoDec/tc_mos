export default [
    {
        component: async () => import('../pages/supplier/AppSupplierShow.vue'),
        meta: {requiresAuth: true},
        name: 'suppliersShow',
        path: '/suppliers/show'
    }
]
