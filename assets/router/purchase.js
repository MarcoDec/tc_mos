import AppShowGui from '../components/pages/AppShowGui.vue'
import AppShowGuiComponent from '../components/pages/purchase/component/show/AppShowGuiComponent.vue'
import AppShowGuiSupplier from '../components/pages/purchase/supplier/show/AppShowGuiSupplier.vue'
import AppTablePageSuspense from '../components/pages/table/AppTablePageSuspense.vue'
import AppTreePageAttribute from '../components/pages/tree/AppTreePageAttribute.vue'
import AppTreePageSuspense from '../components/pages/tree/AppTreePageSuspense.vue'
import {readonly} from 'vue'

export default [
    {
        component: AppTablePageSuspense,
        meta: {title: 'Groupes d\'équivalence — T-Concept GPAO'},
        name: 'component-equivalents',
        path: '/component-equivalents',
        props: {
            apiBaseRoute: 'component-equivalents',
            fields: [
                {create: false, label: 'Code', name: 'code', update: false},
                {label: 'Nom', name: 'name'},
                {label: 'Description', name: 'description'},
                {label: 'Unité', name: 'unit', options: {base: 'units'}, sortName: 'unit.code', type: 'select'},
                {/*create: false,*/ label: 'Famille', name: 'family', options: {base: 'component-families'}, sortName: 'family.code', type: 'select'},
                {
                    create: false,
                    label: 'Items',
                    name: 'components',
                    type: 'multiselect-fetch',
                    api: '/api/components',
                    filteredProperty: 'code',
                    update: true
                }
            ],
            icon: 'magnet',
            sort: readonly({label: 'Nom', name: 'name'}),
            title: 'Equivalences'
        }
    },
    {
        component: AppTablePageSuspense,
        meta: {title: 'Attributs — T-Concept GPAO'},
        name: 'attributes',
        path: '/attributes',
        props: {
            apiBaseRoute: 'attributes',
            fields: [
                {label: 'Nom', name: 'name'},
                {label: 'Description', name: 'description'},
                {
                    label: 'Type',
                    name: 'type',
                    options: [
                        {text: 'Booléen', value: 'bool'},
                        {text: 'Couleur', value: 'color'},
                        {text: 'Entier', value: 'int'},
                        {text: 'Pourcentage', value: 'percent'},
                        {text: 'Texte', value: 'text'},
                        {text: 'Mesure', value: 'measure'},
                        {text: 'MesureSelect', value: 'measureSelect'}
                    ],
                    type: 'select',
                    update: false
                },
                {label: 'Unité', name: 'unit', options: {base: 'units'}, sortName: 'unit.code', type: 'select'},
                {create: false, label: 'Familles', name: 'familiesName', search: false, sort: false, update: false}
            ],
            icon: 'magnet',
            sort: readonly({label: 'Nom', name: 'name'}),
            title: 'Attributs'
        }
    },
    {
        component: AppTreePageSuspense,
        meta: {title: 'Familles de composants — T-Concept GPAO'},
        name: 'component-families',
        path: '/component-families',
        props: {
            fields: [
                {label: 'Parent', name: 'parent', options: {existing: 'component-families'}, type: 'select'},
                {label: 'Code', name: 'code'},
                {label: 'Nom', name: 'name'},
                {label: 'Cuivre', name: 'copperable', type: 'boolean'},
                {label: 'Code douanier', name: 'customsCode'},
                {label: 'Icône', name: 'file', type: 'file'}
            ],
            label: 'Famille de composants',
            tag: AppTreePageAttribute
        }
    },
    {
        component: AppShowGuiSupplier,
        meta: {container: false, title: 'Fournisseur — T-Concept GPAO'},
        name: 'supplier',
        path: '/supplier/:id_supplier'
    },
    {
        component: AppShowGuiComponent,
        meta: {container: false, title: 'Composant — T-Concept GPAO'},
        name: 'component',
        path: '/component/:id_component'
    },
    // {
    //     component: AppShowGui,
    //     meta: {container: false, title: 'Equipement — T-Concept GPAO'},
    //     name: 'equipment',
    //     path: '/equipment'
    // },
    {
        component: AppTablePageSuspense,
        meta: {title: 'Paramètres production— T-Concept GPAO'},
        name: 'purchase parameters',
        path: '/purchase-parameters',
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
            readFilter: '?pagination=false&type=purchase',
            sort: readonly({label: 'Nom', name: 'name'}),
            title: 'Paramètres'
        }
    },
    {
        component: () => import('../components/pages/purchase/component/list/AppComponentPage.vue'),
        meta: {requiresAuth: true},
        name: 'component-list',
        path: '/component-list'
    },
    {
        component: () => import('../components/pages/purchase/supplier/list/AppSupplierListPage.vue'),
        meta: {requiresAuth: true},
        name: 'supplier-list',
        path: '/supplier-list',
        props: {
            icon: 'user-tag',
            title: 'Liste des Fournisseurs'
        }
    },
    {
        component: () => import('../components/pages/purchase/order/show/AppSupplierOrder.vue'),
        meta: {requiresAuth: true},
        name: 'supplier-order-show',
        path: '/purchaseOrder/show/:id'
    },
    {
        component: () => import('../components/pages/purchase/order/list/AppSupplierOrderListPage.vue'),
        meta: {requiresAuth: true},
        name: 'purchaseOrderList',
        path: '/purchaseOrder/list',
        props: {
            icon: 'bullhorn',
            title: 'Commandes Fournisseurs'
        }
    },
]
