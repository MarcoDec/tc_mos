import type {RouteComponent} from 'vue-router'

export default [
    {
        component: async (): Promise<RouteComponent> => import('../pages/app-collection-table/AppCollectionTableWrapper.vue'),
        meta: {requiresAuth: true},
        name: 'Zone-list',
        path: '/Zone/list',
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
                }
            ],
            icon: 'map-marked',
            title: 'Zone'
        }
    },
    {
        component: async (): Promise<RouteComponent> => import('../pages/app-collection-table/AppCollectionTableWrapper.vue'),
        meta: {requiresAuth: true},
        name: 'Group-list',
        path: '/Group/list',
        props: {
            fields: [
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
                },
                {
                    create: false,
                    filter: true,
                    label: 'Organe de sécurité',
                    name: 'organeDeSecurite',
                    sort: true,
                    type: 'boolean',
                    update: true
                },
                {
                    create: false,
                    filter: true,
                    label: 'Type',
                    name: 'type',
                    options: [{text: 'aaaaa', value: 'aaaaa'}, {text: 'bbbb', value: 'bbbb'}],
                    sort: false,
                    type: 'select',
                    update: true
                }
            ],
            icon: 'wrench',
            title: 'Groupe d équipements'
        }
    },
    {
        component: async (): Promise<RouteComponent> => import('../pages/app-collection-table/AppCollectionTableWrapper.vue'),
        meta: {requiresAuth: true},
        name: 'EngineEvent-list',
        path: '/EngineEvent/list',
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
                    label: 'Date',
                    name: 'date',
                    sort: true,
                    type: 'date',
                    update: true
                },
                {
                    create: false,
                    filter: true,
                    label: 'Fait',
                    name: 'fait',
                    sort: true,
                    type: 'boolean',
                    update: true
                },
                {
                    create: false,
                    filter: true,
                    label: 'Intervenant',
                    name: 'intervenant',
                    options: [{text: 'aaaaa', value: 'aaaaa'}, {text: 'bbbb', value: 'bbbb'}],
                    sort: false,
                    type: 'select',
                    update: true
                },
                {
                    create: false,
                    filter: true,
                    label: 'Équipement',
                    name: 'equipement',
                    options: [{text: 'aaaaa', value: 'aaaaa'}, {text: 'bbbb', value: 'bbbb'}],
                    sort: false,
                    type: 'select',
                    update: true
                },
                {
                    create: false,
                    filter: true,
                    label: 'Type',
                    name: 'type',
                    options: [{text: 'aaaaa', value: 'aaaaa'}, {text: 'bbbb', value: 'bbbb'}],
                    sort: false,
                    type: 'select',
                    update: true
                }
            ],
            icon: 'calendar-day',
            title: 'Événement sur un équipement'
        }
    }
]
