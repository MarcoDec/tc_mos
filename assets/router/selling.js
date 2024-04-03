import AppShowGuiCustomer from '../components/pages/selling/customer/AppShowGuiCustomer.vue'
import AppTablePageSuspense from '../components/pages/table/AppTablePageSuspense.vue'
import {readonly} from 'vue'

export default [
    {
        component: AppShowGuiCustomer,
        meta: {container: false, title: 'Client — T-Concept GPAO'},
        name: 'customer',
        path: '/customer/show/:id_customer'
    },
    {
        component: AppTablePageSuspense,
        meta: {title: 'Paramètres Ventes — T-Concept GPAO'},
        name: 'selling parameters',
        path: '/selling-parameters/list',
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
        component: () => import('../components/pages/selling/order/AppCustomerOrders.vue'),
        meta: {requiresAuth: true},
        name: 'customer-order-show',
        path: '/customer-order/show/:id'
    },
    {
        component: () => import('../components/pages/selling/order/AppCustomerOrderListPage.vue'),
        meta: {requiresAuth: true},
        name: 'customer-order-list',
        path: '/customer-order/list',
        props: {
            icon: 'bullhorn',
            title: 'Commandes de vente clients'
        }
    },
    {
        component: () => import('../components/pages/selling/customer/AppCustomerListPage.vue'),
        meta: {requiresAuth: true},
        name: 'customer-list',
        path: '/customer/list',
        props: {
            icon: 'user-tie',
            title: 'Liste des clients'
        }
    }
]
