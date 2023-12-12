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
    },
    {
        component: async () => import('../components/pages/logistic/label/AppLabelTemplateList.vue'),
        meta: {requiresAuth: true},
        name: 'label-template-list',
        path: '/label-template-list',
        props: {}
    },
    {
        component: async () => import('../components/pages/logistic/label/AppLabelTemplateGenerate.vue'),
        name: 'label-template-generate',
        path: '/label-template-generate/:id_label_template',
        props: {}
    },
    {
        component: AppTablePageSuspense,
        meta: {title: 'Etiquettes Générées — T-Concept GPAO'},
        name: 'etiquette-list',
        path: '/etiquette-list',
        props: {
            apiBaseRoute: 'label-cartons',
            fields: [
                {label: 'Numéro de Lot', name: 'batchnumber'},
                {label: 'Site de livraison client', name: 'customerAddressName'},
                {label: 'Point de destination', name: 'customerDestinationPoint'},
                {label: 'Poids total', name: 'grossWeight'},
                {label: 'Poids net', name: 'netWeight'},
                {label: 'Numéro étiquette', name: 'labelNumber'},
                {label: 'Reference Logistique', name: 'logisticReference'},
                {label: 'Fabricant', name: 'manufacturer'},
                {label: 'Désignation Produit', name: 'productDescription'},
                {label: 'Reference Produit', name: 'productReference'},
                {label: 'Indice Produit', name: 'productIndice'},
                {label: 'Quantité', name: 'quantity', type: 'number'},
                {label: 'Site de départ', name: 'shipFromAddressName'},
                {label: 'Référence du Vendeur', name: 'vendorNumber'},
                {label: 'Date', name: 'date', type: 'date'},
                {
                    label: 'Type Etiquette',
                    name: 'labelKind',
                    options: [
                        {text: 'Carton - TCONCEPT', value: 'TConcept'},
                        {text: 'Carton - ETI9', value: 'ETI9'}
                    ],
                    type: 'select',
                    update: true,
                    create: true,
                    search: true
                },
                {label: 'Code ZPL', name: 'zpl', create: false, update: false, sort: false, search: false, type: 'downloadText'},
                {label: 'Image', name: 'url', create: false, update: false, sort: false, search: false, type: 'link'}
                // {label: 'Unité', name: 'unit', options: {base: 'units'}, sortName: 'unit.code', type: 'select'},
                // {create: false, label: 'Familles', name: 'familiesName', search: false, sort: false, update: false}
            ],
            icon: 'tags',
            sort: readonly({label: 'Référence Produit', name: 'productReference'}),
            title: 'Etiquettes Générées'
        }
    }
]
