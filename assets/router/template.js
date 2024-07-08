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
    },
    {
        component: () => import('../components/pages/template/AppPdfDeliveryNote.vue'),
        meta: {requiresAuth: true},
        name: 'pdf-delivery-note',
        path: '/pdf-delivery-note'
    }

]
