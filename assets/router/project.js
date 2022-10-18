import AppTablePageSuspense from '../components/pages/table/AppTablePageSuspense.vue'
import {readonly} from 'vue'

export default [
    {
        component: AppTablePageSuspense,
        meta: {title: 'Opérations — T-Concept GPAO'},
        name: 'project-operations',
        path: '/project-operations',
        props: {
            fields: [
                {label: 'Code', name: 'code'},
                {label: 'Nom', name: 'name'},
                {label: 'Auto', name: 'auto', sort: false, type: 'boolean'},
                {label: 'Limite', name: 'boundary', sort: false},
                {label: 'Cadence', name: 'cadence', search: false, sort: false, type: 'measure'},
                {label: 'Prix', name: 'price', search: false, sort: false, type: 'price'},
                {label: 'Durée', name: 'time', search: false, sort: false, type: 'measure'},
                {label: 'Type', name: 'type', options: {base: 'operation-types'}, sortName: 'type.name', type: 'select'}
            ],
            icon: 'atom',
            sort: readonly({label: 'Code', name: 'code'}),
            title: 'Opérations'
        }
    }
]
