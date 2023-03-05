// import AppTablePageSuspense from '../components/pages/table/AppTablePageSuspense.vue'
// import {readonly} from 'vue'
import AppCustomerPage from './pages/customer/AppCustomerPage.vue'

export default [
    {
        component: () => AppCustomerPage, //import('../pages/customer/AppCustomerPage.vue'),
        meta: {requiresAuth: true},
        name: 'customer-list',
        path: '/customer-list'
    }
]
