import AppEngineList from '../components/pages/production/equipment/list/AppEngineList.vue'
import AppManufacturerEngine from '../components/pages/production/manufacturer/AppManufacturerEngine.vue'
import AppShowGuiTestCounterPart from '../components/pages/production/equipment/show/test-counter-part/AppShowGuiTestCounterPart.vue'
import AppShowGuiTool from '../components/pages/production/equipment/show/tool/AppShowGuiTool.vue'
import AppShowGuiWorkstation from '../components/pages/production/equipment/show/workstation/AppShowGuiWorkstation.vue'
import AppTablePageSuspense from '../components/pages/table/AppTablePageSuspense.vue'
import AppTablePageType from '../components/pages/table/AppTablePageType.vue'
import {readonly} from 'vue'
import AppShowGuiMachine from '../components/pages/production/equipment/show/machine/AppShowGuiMachine.vue'
import AppShowGuiSparePart from '../components/pages/production/equipment/show/spare-part/AppShowGuiSparePart.vue'
import AppShowGuiInfra from '../components/pages/production/equipment/show/infrastructure/AppShowGuiInfra.vue'

const myOptions = [
    {iri: 'counter-part', text: 'Contrepartie de test', value: 'counter-part'},
    {iri: 'workstation', text: 'Poste de travail', value: 'workstation'},
    {iri: 'tool', text: 'Outil', value: 'tool'}
]
export default [
    {
        component: AppTablePageType,
        meta: {title: 'Groupes d\'équipements — T-Concept GPAO'},
        name: 'engine-groups',
        path: '/engine-groups',
        props: {
            apiBaseRoute: 'engine-groups',
            apiTypedRoutes: {
                field: 'type',
                routes: [
                    {url: '/api/counter-part-groups', valeur: 'counter-part'},
                    {url: '/api/workstation-groups', valeur: 'workstation'},
                    {url: '/api/tool-groups', valeur: 'tool'}
                ]
            },
            fields: [
                {label: 'Code', name: 'code'},
                {label: 'Nom', name: 'name'},
                {
                    label: 'Type',
                    name: 'type',
                    options: myOptions,
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
        meta: {title: 'Fabricants Equipement — T-Concept GPAO'},
        name: 'manufacturers',
        path: '/manufacturers',
        props: {
            apiBaseRoute: 'manufacturers',
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
            title: 'Fabricants Equipement'
        }
    },
    {
        component: AppTablePageSuspense,
        meta: {title: 'Zones — T-Concept GPAO'},
        name: 'zones',
        path: '/zones',
        props: {
            apiBaseRoute: 'zones',
            fields: [
                {label: 'Nom', name: 'name'},
                {
                    label: 'Entrepôt',
                    name: 'warehouse',
                    type: 'multiselect-fetch',
                    api: '/api/warehouses',
                    filteredProperty: 'name',
                    permanentFilters: [],
                    max: 1,
                    sort: false
                }
            ],
            icon: 'map-marked',
            isCompanyFiltered: true,
            sort: readonly({label: 'Nom', name: 'name'}),
            title: 'Zones'
        }
    },
    {
        component: AppTablePageSuspense,
        meta: {title: 'Paramètres production— T-Concept GPAO'},
        name: 'production parameters',
        path: '/production-parameters',
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
            readFilter: '?page=1&pagination=false&type=production',
            sort: readonly({label: 'Nom', name: 'name'}),
            title: 'Paramètres'
        }
    },
    {
        component: () => import('../components/pages/production/manufacturingOrder/AppManufacturingOrderPage.vue'),
        meta: {requiresAuth: true},
        name: 'of-list',
        path: '/of-list',
        title: 'Ordres de fabrication'
    },
    {
        component: AppManufacturerEngine,
        meta: {title: 'Références Equipement — T-Concept GPAO'},
        name: 'manufacturer-engines',
        path: '/manufacturer-engines',
        props: {
            title: 'Modèle d\'équipement'
        }
    },
    {
        component: AppEngineList,
        meta: {title: 'Outils — T-Concept GPAO'},
        name: 'tools',
        path: '/tools',
        props: {
            icon: 'toolbox',
            title: 'Outils',
            engineType: 'tool'
        }
    },
    {
        component: AppEngineList,
        meta: {title: 'Postes de travail — T-Concept GPAO'},
        name: 'workstations',
        path: '/workstations',
        props: {
            icon: 'desktop',
            title: 'Postes de travail',
            engineType: 'workstation'
        }
    },
    {
        component: AppEngineList,
        meta: {title: 'Contre-partie de test — T-Concept GPAO'},
        name: 'counter-parts',
        path: '/counter-parts',
        props: {
            icon: 'flask',
            title: 'Contre-partie de test',
            engineType: 'counter-part'
        }
    },
    {
        component: AppEngineList,
        meta: {title: 'Machines — T-Concept GPAO'},
        name: 'machines',
        path: '/machines',
        props: {
            icon: 'cogs',
            title: 'Machines',
            engineType: 'machine'
        }
    },
    {
        component: AppEngineList,
        meta: {title: 'Pièces de rechange — T-Concept GPAO'},
        name: 'spare-parts',
        path: '/spare-parts',
        props: {
            icon: 'puzzle-piece',
            title: 'Pièces de rechange',
            engineType: 'spare-part'
        }
    },
    {
        component: AppEngineList,
        meta: {title: 'Infrastructure — T-Concept GPAO'},
        name: 'infrastructures',
        path: '/infrastructures',
        props: {
            icon: 'building',
            title: 'Elément d\'infrastructure',
            engineType: 'infra'
        }
    },
    {
        component: AppShowGuiTestCounterPart,
        meta: {container: false, title: 'Test Counter Part — T-Concept GPAO'},
        name: 'counterPartShow',
        path: '/counter-part/:id_engine'
    },
    { // <a target="_blank" href="https://icons8.com/icon/EIHtuCnFqwku/wiring">wiring</a> icon by <a target="_blank" href="https://icons8.com">Icons8</a>
        component: AppShowGuiWorkstation,
        meta: {container: false, title: 'Workstation — T-Concept GPAO'},
        name: 'workstationShow',
        path: '/workstation/:id_engine'
    },
    { //screwdriver-wrench
        component: AppShowGuiTool,
        meta: {container: false, title: 'Tool — T-Concept GPAO'},
        name: 'toolShow',
        path: '/tool/:id_engine'
    },
    { //screwdriver-wrench
        component: AppShowGuiMachine,
        meta: {container: false, title: 'Machine — T-Concept GPAO'},
        name: 'machineShow',
        path: '/machine/:id_engine'
    },
    { //screwdriver-wrench
        component: AppShowGuiSparePart,
        meta: {container: false, title: 'Pièce de rechange — T-Concept GPAO'},
        name: 'sparePartShow',
        path: '/spare-part/:id_engine'
    },
    { //screwdriver-wrench
        component: AppShowGuiInfra,
        meta: {container: false, title: 'Equipement d\'infrastructure — T-Concept GPAO'},
        name: 'infraShow',
        path: '/infra/:id_engine'
    },
    {
        component: async () => import('../components/pages/production/Planning/AppProductionPlanningPage.vue'),
        meta: {requiresAuth: true},
        name: 'manufacturing-schedule',
        path: '/manufacturingSchedule',
        props: {
            fields: [
                {
                    label: 'Produit',
                    name: 'produit',
                    type: 'text',
                    colWidth: '800px'
                },
                {
                    label: 'Ind.',
                    name: 'indice',
                    type: 'text'
                },
                {
                    label: 'design',
                    name: 'designation',
                    type: 'text'
                },
                {
                    label: 'compagnie',
                    name: 'compagnie',
                    type: 'text'
                },
                {
                    label: 'client',
                    name: 'client',
                    type: 'text'
                },
                {
                    label: 'Stock',
                    name: 'stock',
                    type: 'text'
                },
                {
                    label: 'T-Chiffrage',
                    name: 'Temps Chiffrage',
                    type: 'text'
                },
                {
                    label: 'T-Atelier',
                    name: 'temps atelier',
                    type: 'text'
                },
                {
                    label: 'VP',
                    name: 'volu_previ',
                    type: 'text'
                },
                {
                    label: '3%VP',
                    name: '3pc_volu_previ',
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
        component: async () => import('./pages/AppManufacturingOrderNeeds.vue'),
        meta: {requiresAuth: true},
        name: 'manufacturing-order-needs',
        path: '/manufacturingOrderDashboard',
        props: {
            fieldsCollapseOfsToConfirm: [
                {
                    label: 'Début Prod.',
                    name: 'debutProd',
                    type: 'date'
                },
                {
                    label: 'OF',
                    name: 'of',
                    type: 'number'
                },
                {
                    label: 'Indice OF',
                    name: 'Indice OF',
                    type: 'number'
                },
                {
                    label: 'Produit',
                    name: 'produit',
                    type: 'text'
                },
                {
                    label: 'Indice',
                    name: 'Indice',
                    type: 'number'
                },
                {
                    label: 'Quantité',
                    name: 'quantite',
                    type: 'text'
                },
                {
                    label: 'Site de production',
                    name: 'siteDeProduction',
                    type: 'text'
                },
                {
                    label: 'Confirmer OF',
                    name: 'confirmedOF',
                    type: 'boolean'
                }
            ],
            fieldsCollapseOnGoingLocalOf: [
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
                    label: 'Produit',
                    name: 'produit',
                    type: 'text'
                },
                {
                    label: 'Indice',
                    name: 'Indice',
                    type: 'number'
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
                    name: 'Etat',
                    type: 'text'
                },
                {
                    label: 'Site de production',
                    name: 'siteDeProduction',
                    type: 'text'
                },
                {
                    label: 'OF',
                    name: 'of',
                    type: 'text'
                },
                {
                    label: 'Indice OF',
                    name: 'Indice OF',
                    type: 'number'
                }
            ],
            fieldsCollapsenewOfs: [
                {
                    label: 'Début Prod. sur site de fabrication',
                    name: 'date',
                    type: 'date',
                    readonly: true
                },
                {
                    label: 'Produit',
                    name: 'product',
                    type: 'text'
                },
                {
                    label: 'Quantité',
                    name: 'quantity',
                    type: 'text'
                },
                {
                    label: 'Site de production',
                    name: 'siteDeProduction',
                    type: 'multiselect-fetch',
                    api: '/api/companies',
                    filteredProperty: 'name',
                    max: 1
                },
                {
                    label: 'Lancer OF',
                    name: 'lancerOF',
                    type: 'boolean'
                }
            ],
            icon: 'table-list',
            title: 'Tableau de bord des OFs du site'
        }
    }
    //,
    // {
    //     component: AppEquipementListEvent,
    //     meta: {container: false, title: 'Evénements Equipements — T-Concept GPAO'},
    //     name: 'engine-events',
    //     path: '/engine-events'
    // }
]
