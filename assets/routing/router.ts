/* eslint-disable consistent-return,@typescript-eslint/prefer-readonly-parameter-types */
import {createRouter, createWebHistory} from 'vue-router'
import type {Getters} from '../store/security'
import type {RouteComponent} from 'vue-router'
import store from '../store'
import {useNamespacedGetters} from 'vuex-composition-helpers'

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
            component: async (): Promise<RouteComponent> => import('./pages/AppCollectionTablePage.vue'),
            name: 'ComponentReferenceValue-list',
            path: '/ComponentReferenceValue/list',
            props: {
                fields: [
                    {
                        create: true,
                        filter: true,
                        label: 'Composant',
                        name: 'composant',
                        options: [{text: 'aaaaa', value: 'aaaaa'}, {text: 'bbbb', value: 'bbbb'}],
                        sort: false,
                        type: 'select',
                        update: true
                    },
                    {
                        create: true,
                        filter: true,
                        label: 'Section',
                        name: 'section',
                        sort: true,
                        type: 'number',
                        update: false
                    },
                    {
                        create: false,
                        filter: true,
                        label: 'Obligation hauteur',
                        name: 'obligationHauteur',
                        sort: false,
                        type: 'boolean',
                        update: true
                    },
                    {
                        create: false,
                        filter: true,
                        label: 'Hauteur',
                        name: 'hauteur',
                        sort: true,
                        type: 'number',
                        update: false
                    },
                    {
                        create: true,
                        filter: true,
                        label: 'Tolérance hauteur',
                        name: 'toleranceHauteur',
                        sort: false,
                        type: 'number',
                        update: true
                    },
                    {
                        create: false,
                        filter: true,
                        label: 'Obligation largeur',
                        name: 'obligationLargeur',
                        sort: false,
                        type: 'boolean',
                        update: true
                    },
                    {
                        create: true,
                        filter: true,
                        label: 'Largeur',
                        name: 'largeur',
                        sort: false,
                        type: 'number',
                        update: true
                    },
                    {
                        create: true,
                        filter: true,
                        label: 'Tolérence largeur',
                        name: 'tolerenceLargeur',
                        sort: true,
                        type: 'number',
                        update: true
                    },
                    {
                        create: false,
                        filter: true,
                        label: 'Obligation traction',
                        name: 'obligationTraction',
                        sort: false,
                        type: 'boolean',
                        update: true
                    },
                    {
                        create: true,
                        filter: true,
                        label: 'Traction',
                        name: 'traction',
                        sort: true,
                        type: 'number',
                        update: true
                    },
                    {
                        create: true,
                        filter: true,
                        label: 'Tolérance traction',
                        name: 'toleranceTraction',
                        sort: true,
                        type: 'number',
                        update: true
                    }
                ],
                icon: 'check-circle',
                title: 'Relevé qualité composant'
            }
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/AppCollectionTablePage.vue'),
            name: 'attribute-list',
            path: '/Attribute/list',
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
                        label: 'Description',
                        name: 'description',
                        sort: true,
                        type: 'text',
                        update: false
                    },
                    {
                        create: false,
                        filter: true,
                        label: 'Unité',
                        name: 'unité',
                        options: [{text: 'aaaaa', value: 'aaaaa'}, {text: 'bbbb', value: 'bbbb'}],
                        sort: true,
                        type: 'select',
                        update: true
                    },
                    {
                        create: false,
                        filter: true,
                        label: 'Familles',
                        name: 'familles',
                        options: [{text: 'aaaaa', value: 'aaaaa'}, {text: 'bbbb', value: 'bbbb'}],
                        sort: true,
                        type: 'select',
                        update: true
                    }
                ],
                icon: 'magnet',
                title: 'Attribut'
            }
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/AppCollectionTablePage.vue'),
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
            component: async (): Promise<RouteComponent> => import('./pages/AppCollectionTablePage.vue'),
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
            component: async (): Promise<RouteComponent> => import('./pages/AppCollectionTablePage.vue'),
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
            component: async (): Promise<RouteComponent> => import('./pages/AppCollectionTablePage.vue'),
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
            component: async (): Promise<RouteComponent> => import('./pages/AppCollectionTablePage.vue'),
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
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/AppCollectionTablePage.vue'),
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
            component: async (): Promise<RouteComponent> => import('./pages/AppCollectionTablePage.vue'),
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
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/AppCollectionTablePage.vue'),
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
            component: async (): Promise<RouteComponent> => import('./pages/AppCollectionTablePage.vue'),
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
            component: async (): Promise<RouteComponent> => import('./pages/AppCollectionTablePage.vue'),
            name: 'RejectType-list',
            path: '/RejectType/list',
            props: {
                fields: [
                    {
                        create: false,
                        filter: true,
                        label: 'ID',
                        name: 'id',
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
                icon: 'elementor',
                title: 'Catégorie de rejet de production'
            }
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/AppCollectionTablePage.vue'),
            name: 'QualityType-list',
            path: '/QualityType/list',
            props: {
                fields: [
                    {
                        create: false,
                        filter: true,
                        label: 'ID',
                        name: 'id',
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
                icon: 'elementor',
                title: 'Critère qualité'
            }
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/AppCollectionTablePage.vue'),
            name: 'OutTrainer-list',
            path: '/OutTrainer/list',
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
                        label: 'Prénom',
                        name: 'prenom',
                        sort: true,
                        type: 'text',
                        update: true
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
                        label: 'Ville',
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
                        sort: false,
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
                icon: 'user-graduate',
                title: 'Formateur extérieur'
            }
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/AppCollectionTablePage.vue'),
            name: 'EmployeeEventType-list',
            path: '/EmployeeEventType/list',
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
                        label: 'Vers le statut',
                        name: 'versLeStatut',
                        sort: true,
                        type: 'text',
                        update: true
                    }
                ],
                icon: 'elementor',
                title: 'Catégories d événement des employés'
            }
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/AppCollectionTablePage.vue'),
            name: 'TimeSlot-list',
            path: '/TimeSlot/list',
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
                        label: 'Début',
                        name: 'debut',
                        sort: true,
                        type: 'time',
                        update: true
                    },
                    {
                        create: false,
                        filter: true,
                        label: 'Début de la pause',
                        name: 'debutPause',
                        sort: true,
                        type: 'time',
                        update: true
                    },
                    {
                        create: false,
                        filter: true,
                        label: 'Fin de la pause',
                        name: 'finPause',
                        sort: true,
                        type: 'time',
                        update: true
                    },
                    {
                        create: false,
                        filter: true,
                        label: 'Fin',
                        name: 'fin',
                        sort: true,
                        type: 'time',
                        update: true
                    }
                ],
                icon: 'clock',
                title: 'Plage horaire'
            }
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/AppCollectionTablePage.vue'),
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
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/AppCollectionTablePage.vue'),
            name: 'ITRequest-list',
            path: '/ITRequest/list',
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
                        label: 'État',
                        name: 'etat',
                        sort: true,
                        type: 'text',
                        update: true
                    },
                    {
                        create: false,
                        filter: true,
                        label: 'Délai',
                        name: 'delai',
                        sort: true,
                        type: 'date',
                        update: true
                    },
                    {
                        create: false,
                        filter: true,
                        label: 'Version',
                        name: 'version',
                        sort: true,
                        type: 'text',
                        update: true
                    },
                    {
                        create: false,
                        filter: true,
                        label: 'Demandé par',
                        name: 'demandePar',
                        options: [{text: 'aaaaa', value: 'aaaaa'}, {text: 'bbbb', value: 'bbbb'}],
                        sort: false,
                        type: 'select',
                        update: true
                    },
                    {
                        create: false,
                        filter: true,
                        label: 'Demandé le',
                        name: 'demandeLe',
                        sort: true,
                        type: 'date',
                        update: true
                    }
                ],
                icon: 'laptop-code',
                title: 'Demande'
            }
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/AppCollectionTablePage.vue'),
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
        }
    ]
})

router.beforeEach(to => {
    if (
        to.matched.some(record => record.meta.requiresAuth && record.name !== 'login')
        && !useNamespacedGetters<Getters>(store, 'users', ['hasUser']).hasUser.value
    )
        return {name: 'login'}
})

export default router
