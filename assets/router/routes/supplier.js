export default [
    {
        component: () => import('../pages/supplier/AppSupplierPage.vue'),
        meta: {requiresAuth: true},
        name: 'supplier-list',
        path: '/supplier-list'
    }
]
