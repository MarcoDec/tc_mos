import AppTablePageSuspense from '../components/pages/table/AppTablePageSuspense.vue'
import AppTablePageType from '../components/pages/table/AppTablePageType.vue'
import {readonly} from 'vue'

export default [
    {
        component: AppTablePageType,
        meta: {title: 'Groupes d\'équipements — T-Concept GPAO'},
        name: 'engine-groups',
        path: '/engine-groups',
        props: {
            fields: [
                {label: 'Code', name: 'code'},
                {label: 'Nom', name: 'name'},
                {
                    label: 'Type',
                    name: '@type',
                    options: [
                        {iri: 'engine-groups', text: '', value: null},
                        {iri: 'counter-part-groups', text: 'Contrepartie de test', value: 'CounterPartGroup'},
                        {iri: 'workstation-groups', text: 'Poste de travail', value: 'WorkstationGroup'},
                        {iri: 'tool-groups', text: 'Outil', value: 'ToolGroup'}
                    ],
                    sort: false,
                    type: 'select',
                    update: false
                }
            ],
            icon: 'wrench',
            sort: readonly({label: 'Code', name: 'code'}),
            title: 'Groupes d\'équipements'
        }
    },
    {
        component: AppTablePageSuspense,
        meta: {title: 'Fabricants — T-Concept GPAO'},
        name: 'manufacturers',
        path: '/manufacturers',
        props: {
            fields: [
                {label: 'Nom', name: 'name'},
                {
                    label: 'Société',
                    name: 'society',
                    options: {base: 'societies'},
                    sortName: 'society.name',
                    type: 'select'
                }
            ],
            icon: 'oil-well',
            sort: readonly({label: 'Nom', name: 'name'}),
            title: 'Fabricants'
        }
    },
    {
        component: AppTablePageSuspense,
        meta: {title: 'Zones — T-Concept GPAO'},
        name: 'zones',
        path: '/zones',
        props: {
            fields: [{label: 'Nom', name: 'name'}],
            icon: 'map-marked',
            sort: readonly({label: 'Nom', name: 'name'}),
            title: 'Zones'
        }
    },

    {
        component: async () => import('./pages/production/AppWarehouseList.vue'),
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
                    name: 'famille',
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
        component: async () => import('./pages/production/AppWarehouseShow.vue'),
        meta: {requiresAuth: true},
        name: 'warehouse-show',
        path: '/warehouse-show',
        props: {
            icon: 'warehouse',
            title: 'Entrepot'
        }
    }
]
