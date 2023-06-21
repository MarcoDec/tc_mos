export default [
    {
        component: async () => import('../pages/supplier/AppSupplierShowInlist.vue'),
        meta: {requiresAuth: true},
        name: 'suppliersShow',
        path: '/suppliers/show/:id_supplier'
    }
]
