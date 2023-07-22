export default [
    {
        component: () => import('../../components/pages/selling/customer/AppCustomerFormShow.vue'),
        meta: {requiresAuth: true},
        name: 'customerFormShow',
        path: '/customer/formshow'
    }
]
