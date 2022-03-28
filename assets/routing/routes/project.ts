import type {RouteComponent} from 'vue-router'

export default [
    {
        component: async (): Promise<RouteComponent> => import('../pages/app-collection-table/AppCollectionTableWrapper.vue'),
        meta: {requiresAuth: true},
        name: 'operation-list',
        path: '/operation/list',
        props: {
            fields: [
                {
                    create: true,
                    filter: true,
                    label: 'Code',
                    name: 'code',
                    sort: true,
                    type: 'text',
                    update: true
                },
                {
                    create: false,
                    filter: true,
                    label: 'Nom',
                    name: 'name',
                    options: [{text: 'aaaaa', value: 'aaaaa'}, {text: 'bbbb', value: 'bbbb'}],
                    sort: false,
                    type: 'select',
                    update: true
                },
                {
                    create: true,
                    filter: true,
                    label: 'Type',
                    name: 'type',
                    sort: true,
                    type: 'number',
                    update: false
                },
                {
                    create: false,
                    filter: true,
                    label: 'Auto',
                    name: 'auto',
                    sort: false,
                    type: 'boolean',
                    update: true
                },
                {
                    create: false,
                    filter: true,
                    label: 'Limite',
                    name: 'limite',
                    sort: true,
                    type: 'text',
                    update: true
                },
                {
                    create: false,
                    filter: true,
                    label: 'cadence',
                    name: 'cadence',
                    sort: true,
                    type: 'number',
                    update: false
                },
                {
                    create: true,
                    filter: true,
                    label: 'Prix',
                    name: 'prix',
                    sort: false,
                    type: 'number',
                    update: true
                },
                {
                    create: true,
                    filter: true,
                    label: 'Temps(en ms)',
                    name: 'Temps',
                    sort: false,
                    type: 'date',
                    update: false
                }
            ],
            icon: 'atom',
            title: 'Opération'
        }
    },
    {
        component: async (): Promise<RouteComponent> => import('../pages/app-collection-table/AppCollectionTableWrapper.vue'),
        meta: {requiresAuth: true},
        name: 'OperationType-list',
        path: '/OperationType/list',
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
                    label: 'Assemblage',
                    name: 'assemblage',
                    sort: true,
                    type: 'boolean',
                    update: true
                },
                {
                    create: false,
                    filter: true,
                    label: 'Familles',
                    name: 'familles',
                    options: [{text: 'aaaaa', value: 'aaaaa'}, {text: 'bbbb', value: 'bbbb'}],
                    sort: false,
                    type: 'select',
                    update: true
                }
            ],
            icon: 'elementor',
            title: 'Type d opération'
        }
    },
    {
        component: async (): Promise<RouteComponent> => import('../pages/tree/AppTreePageWrapper.vue'),
        meta: {requiresAuth: true},
        name: 'product-families',
        path: '/product-families',
        props: {title: 'produits', type: 'Produits', url: '/api/product-families'}
    }
]
