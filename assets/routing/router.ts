import {createRouter, createWebHistory} from 'vue-router'
import type {RouteComponent} from 'vue-router'

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            component: async (): Promise<RouteComponent> => import('./pages/tree/AppTreePageWrapper.vue'),
            meta: {requiresAuth: true},
            name: 'component-families',
            path: '/component-families',
            props: {
                extraFields: [
                    {label: 'Code', name: 'code', type: 'text'},
                    {label: 'Cuivre', name: 'copperable', type: 'boolean'}
                ],
                title: 'composants',
                type: 'Composants',
                url: '/api/component-families'
            }
        },
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
            component: async (): Promise<RouteComponent> => import('./pages/tree/AppTreePageWrapper.vue'),
            meta: {requiresAuth: true},
            name: 'product-families',
            path: '/product-families',
            props: {title: 'produits', type: 'Produits', url: '/api/product-families'}
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/AppProductionPlanning.vue'),
            meta: {requiresAuth: true},
            name: 'manufacturing-schedule',
            path: '/manufacturingSchedule',
            props: {
                fields: [

                    {
                        label: 'Produit',
                        name: 'produit',
                        type: 'text'
                    },
                    {
                        label: 'Ind.',
                        name: 'ind',
                        type: 'text'
                    },
                    {
                        label: 'Client',
                        name: 'client',
                        type: 'text'
                    },
                    {
                        label: 'Stocks',
                        name: 'stocks',
                        type: 'text'
                    },
                    {
                        label: '3%VP',
                        name: 'vp',
                        type: 'text'
                    },
                    {
                        label: 'Retard',
                        name: 'retard',
                        type: 'text'
                    }
                ],
                icon: 'table-list',
                title: 'Planning de production'
            }
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/AppManufacturingOrderNeeds.vue'),
            meta: {requiresAuth: true},
            name: 'manufacturing-order-needs',
            path: '/manufacturingOrderNeeds',
            props: {
                fieldsCollapsenewOfs: [

                    {
                        label: 'Client',
                        name: 'client',
                        type: 'text'
                    },
                    {
                        label: 'Produit',
                        name: 'produit',
                        type: 'text'
                    },
                    {
                        label: 'Cmde',
                        name: 'cmde',
                        type: 'text'
                    },
                    {
                        label: 'Minimum de lancement',
                        name: 'minDeLancement',
                        type: 'number'
                    },
                    {
                        label: 'Qté demandée',
                        name: 'qteDemandee',
                        type: 'number'
                    },
                    {
                        label: 'OFs associés',
                        name: 'ofsAssocies',
                        type: 'text'
                    },
                    {
                        label: 'Début Prod.',
                        name: 'debutProd',
                        type: 'date'
                    },
                    {
                        label: 'Fin Prod.',
                        name: 'finProd',
                        type: 'date'
                    },
                    {
                        label: 'Quantité',
                        name: 'quantite',
                        type: 'text'
                    },
                    {
                        label: 'Site de production',
                        name: 'siteDeProduction',
                        options: [{text: 'auto', value: 'auto'}, {text: 'TUNISIE CONCEPT', value: 'TunisieConcept'}],
                        type: 'select'
                    },
                    {
                        label: 'Etat initial OF',
                        name: 'etatInitialOF',
                        options: [{text: 'confirmé', value: 'confirmé'}, {text: 'brouillon', value: 'brouillon'}],
                        type: 'select'
                    },
                    {
                        label: 'Lancer OF',
                        name: 'lancerOF',
                        type: 'boolean'
                    }
                ],
                fieldsCollapseOfsToConfirm: [

                    {
                        label: 'Client',
                        name: 'client',
                        type: 'text'
                    },
                    {
                        label: 'Produit',
                        name: 'produit',
                        type: 'text'
                    },
                    {
                        label: 'Cmde',
                        name: 'cmde',
                        type: 'text'
                    },
                    {
                        label: 'OF',
                        name: 'of',
                        type: 'number'
                    },
                    {
                        label: 'Début Prod.',
                        name: 'debutProd',
                        type: 'date'
                    },
                    {
                        label: 'Fin Prod.',
                        name: 'finProd',
                        type: 'date'
                    },
                    {
                        label: 'Quantité',
                        name: 'quantite',
                        type: 'number'
                    },
                    {
                        label: 'Site de production',
                        name: 'siteDeProduction',
                        type: 'text'
                    },
                    {
                        label: 'Quantité produite',
                        name: 'quantiteProduite',
                        type: 'number'
                    },
                    {
                        label: 'Confirmer OF',
                        name: 'confirmerOF',
                        type: 'boolean'
                    }
                ],
                fieldsCollapseOnGoingLocalOf: [

                    {
                        label: 'Client',
                        name: 'client',
                        type: 'text'
                    },
                    {
                        label: 'Produit',
                        name: 'produit',
                        type: 'text'
                    },
                    {
                        label: 'Cmde',
                        name: 'cmde',
                        type: 'text'
                    },
                    {
                        label: 'OF',
                        name: 'of',
                        type: 'number'
                    },
                    {
                        label: 'Début Prod.',
                        name: 'debutProd',
                        type: 'date'
                    },
                    {
                        label: 'Fin Prod.',
                        name: 'finProd',
                        type: 'date'
                    },
                    {
                        label: 'Quantité',
                        name: 'quantite',
                        type: 'number'
                    },
                    {
                        label: 'Quantité Produite',
                        name: 'quantiteProduite',
                        type: 'number'
                    },
                    {
                        label: 'Etat',
                        name: 'etat',
                        type: 'text'
                    }
                ],
                icon: 'table-list',
                title: 'Calcul des besoins'
            }
        }
    ]
})

export default router
