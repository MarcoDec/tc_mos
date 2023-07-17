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
                    options: [
                        {iri: 'counter-part', text: 'Contrepartie de test', value: 'counter-part'},
                        {iri: 'workstation', text: 'Poste de travail', value: 'workstation'},
                        {iri: 'tool', text: 'Outil', value: 'tool'}
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
            title: 'Fabricants'
        }
    },
    {
        component: AppTablePageSuspense,
        meta: {title: 'Zones — T-Concept GPAO'},
        name: 'zones',
        path: '/zones',
        props: {
            apiBaseRoute: 'zones',
            fields: [{label: 'Nom', name: 'name'}],
            icon: 'map-marked',
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
    }
]
