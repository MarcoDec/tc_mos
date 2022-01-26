import {createRouter, createWebHistory} from 'vue-router'
import type {RouteComponent} from 'vue-router'

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            component: async (): Promise<RouteComponent> => import('./pages/AppHome'),
            meta: {requiresAuth: true},
            name: 'home',
            path: '/'
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/security/AppLogin.vue'),
            meta: {requiresAuth: false},
            name: 'login',
            path: '/login'
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/purchase/supplier/AppSupplierOrder.vue'),
            meta: {requiresAuth: true},
            name: 'orderSupplier',
            path: '/orderSupplier',
            props: {
                fields: [
                    {
                        label: 'N',
                    },
                    {
                        create: false,
                        filter: true,
                        label: 'Composant',
                        name: 'composant',
                        options: [{text: 'aaaaa', value: 'aaaaa'}, {text: 'bbbb', value: 'bbbb'}],
                        sort: false,
                        type: 'select',
                        update: true
                    },
                    {
                        create: false,
                        filter: true,
                        label: 'Produit',
                        name: 'produit',
                        options: [{text: 'aaaaa', value: 'aaaaa'}, {text: 'bbbb', value: 'bbbb'}],
                        sort: false,
                        type: 'select',
                        update: true
                    },
                    {
                        create: true,
                        filter: true,
                        label: 'Référence Fournisseur',
                        name: 'ref',
                        sort: true,
                        type: 'text',
                        update: false
                    },
                    {
                        create: true,
                        filter: true,
                        label: 'Quantité Souhaitée',
                        name: 'quantiteS',
                        sort: true,
                        type: 'text',
                        update: false
                    },
                    {
                        create: true,
                        filter: true,
                        label: 'Date Souhaitée',
                        name: 'date',
                        sort: false,
                        type: 'date',
                        update: false
                    },
                    {
                        create: true,
                        filter: true,
                        label: 'Quantité',
                        name: 'quantite',
                        sort: true,
                        type: 'text',
                        update: false
                    },
                    {
                        create: true,
                        filter: true,
                        label: 'Date de confirmation',
                        name: 'date',
                        sort: false,
                        type: 'date',
                        update: false
                    },
                    {
                        create: true,
                        filter: true,
                        label: 'Etat',
                        name: 'etat',
                        sort: true,
                        type: 'text',
                        update: false
                    },
                    {
                        create: true,
                        filter: true,
                        label: 'Texte',
                        name: 'texte',
                        sort: true,
                        type: 'text',
                        update: false
                    },
                    {
                        create: false,
                        filter: true,
                        label: 'Compagnie destinataire',
                        name: 'compagnie',
                        options: [{text: 'aaaaa', value: 'aaaaa'}, {text: 'bbbb', value: 'bbbb'}],
                        sort: false,
                        type: 'select',
                        update: true
                    },
                ],
                icon: 'shopping-cart',
                title: 'OrderSupplier'
            }
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/AppCollectionTablePage.vue'),
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
                        create: true,
                        filter: false,
                        label: 'Limite',
                        name: 'limite',
                        sort: false,
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
        }
    ]
})

export default router
