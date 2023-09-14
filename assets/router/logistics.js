import AppShowGuiWarehouse from '../components/pages/logistic/warehouse/AppShowGuiWarehouse.vue'
import AppShowGuiWarehouse from '../components/pages/logistic/warehouse/AppShowGuiWarehouse.vue'
import AppTablePageSuspense from '../components/pages/table/AppTablePageSuspense.vue'
import {readonly} from 'vue'

export default [
    {
        component: AppTablePageSuspense,
        meta: {title: 'Transporteurs — T-Concept GPAO'},
        name: 'carriers',
        path: '/carriers',
        props: {
            apiBaseRoute: 'carriers',
            fields: [
                {label: 'Nom', name: 'name'},
                {label: 'Adresse', name: 'address.address'},
                {label: 'Complément d\'adresse', name: 'address.address2'},
                {label: 'Ville', name: 'address.city'},
                {label: 'Code postal', name: 'address.zipCode', sort: false},
                {label: 'Pays', name: 'address.country', options: {base: 'countries', value: 'code'}, type: 'select'},
                {label: 'Numéro de téléphone', name: 'address.phoneNumber', sort: false},
                {label: 'E-mail', name: 'address.email'}
            ],
            icon: 'shuttle-van',
            sort: readonly({label: 'Nom', name: 'name'}),
            title: 'Transporteurs'
        }
    },
    {
        component: AppTablePageSuspense,
        meta: {title: 'Incoterms — T-Concept GPAO'},
        name: 'incoterms',
        path: '/incoterms',
        props: {
            apiBaseRoute: 'incoterms',
            fields: [{label: 'Code', name: 'code'}, {label: 'Nom', name: 'name'}],
            icon: 'file-contract',
            sort: readonly({label: 'Code', name: 'code'}),
            title: 'Incoterms'
        }
    },
    {
        component: async () => import('../components/pages/logistic/warehouse/AppWarehouseList.vue'),
        meta: {requiresAuth: true},
        name: 'warehouse-list',
        path: '/warehouse-list',
        props: {
            fields: [
                {
                    create: false,
                    filter: true,
                    label: 'Nom',
                    name: 'name',
                    sort: true,
                    type: 'text',
                    update: true
                },
                {
                    create: false,
                    filter: true,
                    label: 'Famille',
                    name: 'getFamilies',
                    // options: [{text: 'prison', value: 'prison'}, {text: 'production', value: 'production'}, {text: 'réception', value: 'réception'}, {text: 'magasin piéces finies', value: 'magasinPiécesFinies'}, {text: 'expédition', value: 'expédition'}, {text: 'magasin matières premiéres', value: 'magasinMatièresPremiéres'}, {text: 'camion', value: 'camion'}],
                    sort: false,
                    type: 'text',
                    update: true
                }
            ],
            icon: 'warehouse',
            title: 'Listes Entrepots'
        }
    },
    {
        component: AppShowGuiWarehouse,
        meta: {container: false, title: 'Entrepot — T-Concept GPAO'},
        name: 'warehouse-show',
        path: '/warehouse/:id_warehouse',
        props: {
            icon: 'warehouse',
            title: 'Entrepot',
            brands: true,
            fields: [
                {label: 'Name', name: 'name'},
                {
                    label: 'Company',
                    name: 'company',
                    options: {base: 'company'},
                    search: false,
                    sort: false,
                    type: 'select'
                },
                {
                    label: 'Destination',
                    name: 'destination'
                },
                {
                    label: 'Familles',
                    name: 'families',
                    options: {base: 'warehouses'},
                    type: 'multiselect'

                },
                {
                    label: 'Qualite',
                    name: 'qualite',
                    search: false,
                    sort: false,
                    type: 'measure'
                }
            ]
        }
    }
]
