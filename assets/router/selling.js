import AppShowGuiCustomer from '../components/pages/selling/customer/AppShowGuiCustomer.vue'
import AppTablePageSuspense from '../components/pages/table/AppTablePageSuspense.vue'
import {readonly} from 'vue'

export default [
    {
        component: AppShowGuiCustomer,
        meta: {container: false, title: 'Client — T-Concept GPAO'},
        name: 'customer',
        path: '/customer/:id_customer'
    },
    {
        component: AppTablePageSuspense,
        meta: {title: 'Paramètres Ventes — T-Concept GPAO'},
        name: 'selling parameters',
        path: '/selling-parameters',
        props: {
            apiBaseRoute: 'parameters',
            disableAdd: true,
            disableRemove: true,
            fields: [
                {label: 'Nom', name: 'name', update: false},
                {label: 'Description', name: 'description', type: 'textarea'},
                {label: 'Type', name: 'kind', type: 'text', update: false},
                {label: 'Valeur', name: 'value', type: 'text'}
            ],
            icon: 'gear',
            readFilter: '?page=1&pagination=false&type=selling',
            sort: readonly({label: 'Nom', name: 'name'}),
            title: 'Paramètres'
        }
    },
    {
<<<<<<< HEAD
        component: () => import('./pages/customer/AppCustomerOrders.vue'),
        meta: {requiresAuth: true},
        name: 'customer-order-show',
        path: '/customerorder/show/:id'
=======
        component: () => import('../components/pages/selling/customer/AppCustomerListPage.vue'),
        meta: {requiresAuth: true},
        name: 'customer-list',
        path: '/customer-list',
        props: {
            icon: 'user-tie',
            title: 'Liste des clients'
        }
>>>>>>> develop
    }
]
