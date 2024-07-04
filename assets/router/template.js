export default [
    {
        component: () => import('../components/pages/template/AppPdfPurchaseOrders.vue'),
        meta: {requiresAuth: true},
        name: 'pdf-purchase-orders',
        path: '/pdf-purchase-orders'
    },
    {
        component: () => import('../components/pages/template/AppCustomerOrderConfirmation.vue'),
        meta: {requiresAuth: true},
        name: 'pdf-customer-order-confirmation',
        path: '/pdf-customer-order-confirmation'
    }
]
