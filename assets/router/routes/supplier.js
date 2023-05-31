export default [
    {
        component: () => import('../../components/pages/purchase/supplier/AppSupplierFormShow.vue'),
        meta: {requiresAuth: true},
        name: 'supplierFormShow',
        path: '/supplier/formshow'
    }
]
