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
    },
    {
        component: () => import('../components/pages/template/AppPdfProForma.vue'),
        meta: {requiresAuth: true},
        name: 'pdf-pro-forma',
        path: '/pdf-pro-forma'
    },
    {
        component: () => import('../components/pages/template/AppPdfCreditCustomer.vue'),
        meta: {requiresAuth: true},
        name: 'pdf-credit-customer',
        path: '/pdf-credit-customer'
    }

]
