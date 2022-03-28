import type {RouteComponent} from 'vue-router'

export default [
    {
        component: async (): Promise<RouteComponent> => import('../pages/app-collection-table/AppCollectionTableWrapper.vue'),
        meta: {requiresAuth: true},
        name: 'Carrier-list',
        path: '/Carrier/list',
        props: {
            fields: [
                {
                    create: true,
                    filter: true,
                    label: 'Nom',
                    name: 'name',
                    sort: true,
                    type: 'text',
                    update: false
                },
                {
                    create: false,
                    filter: true,
                    label: 'Adresse',
                    name: 'adresse',
                    sort: true,
                    type: 'text',
                    update: true
                },
                {
                    create: false,
                    filter: true,
                    label: 'Complément d adresse',
                    name: 'complementAdresse',
                    sort: true,
                    type: 'text',
                    update: true
                },
                {
                    create: false,
                    filter: true,
                    label: 'Code postal',
                    name: 'codePostal',
                    sort: true,
                    type: 'text',
                    update: true
                },
                {
                    create: false,
                    filter: true,
                    label: 'ville',
                    name: 'ville',
                    sort: true,
                    type: 'text',
                    update: true
                },
                {
                    create: false,
                    filter: true,
                    label: 'Pays',
                    name: 'pays',
                    options: [{text: 'aaaaa', value: 'aaaaa'}, {text: 'bbbb', value: 'bbbb'}],
                    sort: true,
                    type: 'select',
                    update: true
                },
                {
                    create: false,
                    filter: true,
                    label: 'Téléphone',
                    name: 'telephone',
                    sort: true,
                    type: 'text',
                    update: true
                },
                {
                    create: false,
                    filter: true,
                    label: 'E-mail',
                    name: 'email',
                    sort: true,
                    type: 'text',
                    update: true
                }
            ],
            icon: 'shuttle-van',
            title: 'Transporteur'
        }
    },
    {
        component: async (): Promise<RouteComponent> => import('../pages/app-collection-table/AppCollectionTableWrapper.vue'),
        meta: {requiresAuth: true},
        name: 'Incoterms-list',
        path: '/Incoterms/list',
        props: {
            fields: [
                {
                    create: true,
                    filter: true,
                    label: 'ID',
                    name: 'id',
                    sort: true,
                    type: 'number',
                    update: false
                },
                {
                    create: false,
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
                    sort: true,
                    type: 'text',
                    update: true
                }
            ],
            icon: 'file-contract',
            title: 'Incoterms'
        }
    }
]
