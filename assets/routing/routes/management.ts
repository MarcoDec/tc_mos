import type {RouteComponent} from 'vue-router'

export default [
    {
        component: async (): Promise<RouteComponent> => import('../pages/AppCollectionTablePage.vue'),
        meta: {requiresAuth: true},
        name: 'color-list',
        path: '/color/list',
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
                    create: true,
                    filter: true,
                    label: 'RGB',
                    name: 'rgb',
                    sort: true,
                    type: 'color',
                    update: false
                },
                {
                    create: false,
                    filter: true,
                    label: 'RAL',
                    name: 'ral',
                    sort: true,
                    type: 'text',
                    update: true
                }
            ],
            icon: 'palette',
            title: 'Couleur'
        }
    },
    {
        component: async (): Promise<RouteComponent> => import('../pages/AppCollectionTablePage.vue'),
        meta: {requiresAuth: true},
        name: 'invoiceTimeDue-list',
        path: '/InvoiceTimeDue/list',
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
                    create: true,
                    filter: true,
                    label: 'Jours',
                    name: 'jours',
                    sort: true,
                    type: 'number',
                    update: false
                },
                {
                    create: false,
                    filter: true,
                    label: 'Fin du mois',
                    name: 'finDuMois',
                    sort: false,
                    type: 'boolean',
                    update: true
                },
                {
                    create: true,
                    filter: true,
                    label: 'Jours après la fin du mois',
                    name: 'joursApresLaFinDeMOis',
                    sort: true,
                    type: 'number',
                    update: false
                }
            ],
            icon: 'hourglass-half',
            title: 'Délai de paiement d une facture'
        }
    },
    {
        component: async (): Promise<RouteComponent> => import('../pages/AppCollectionTablePage.vue'),
        meta: {requiresAuth: true},
        name: 'Printer-list',
        path: '/Printer/list',
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
                    label: 'IP',
                    name: 'ip',
                    sort: true,
                    type: 'text',
                    update: true
                },
                {
                    create: true,
                    filter: true,
                    label: 'Compagnie',
                    name: 'compagnie',
                    sort: true,
                    type: 'number',
                    update: false
                }
            ],
            icon: 'print',
            title: 'Imprimante'
        }
    },
    {
        component: async (): Promise<RouteComponent> => import('../pages/AppCollectionTablePage.vue'),
        meta: {requiresAuth: true},
        name: 'Unit-list',
        path: '/Unit/list',
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
                    label: 'Code',
                    name: 'code',
                    sort: true,
                    type: 'text',
                    update: true
                }
            ],
            icon: 'ruler-horizontal',
            title: 'Unité'
        }
    },
    {
        component: async (): Promise<RouteComponent> => import('../pages/AppCollectionTablePage.vue'),
        meta: {requiresAuth: true},
        name: 'VatMessage-list',
        path: '/VatMessage/list',
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
                    label: 'Message',
                    name: 'message',
                    sort: true,
                    type: 'text',
                    update: true
                }
            ],
            icon: 'comments-dollar',
            title: 'Message TVA'
        }
    }
]
