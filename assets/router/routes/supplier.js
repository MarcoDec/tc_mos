export default [
    {
        component: () => import('../pages/supplier/AppSupplierFormShow.vue'),
        meta: {requiresAuth: true},
        name: 'supplierFormShow',
        path: '/supplier/formshow'
    }
]
