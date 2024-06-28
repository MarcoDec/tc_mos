export default [
    {
        component: () => import('../components/pages/Apptemplate.vue'),
        meta: {requiresAuth: true},
        name: 'template',
        path: '/template'
    },
    {
        component: () => import('../components/pages/template/AppPdfPurchaseOrders.vue'),
        meta: {requiresAuth: true},
        name: 'pdf-purchase-orders',
        path: '/pdf-purchase-orders'
    }
]
