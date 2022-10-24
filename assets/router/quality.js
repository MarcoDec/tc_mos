import AppTablePageSuspense from '../components/pages/table/AppTablePageSuspense.vue'
import {readonly} from 'vue'

export default [
    {
        component: AppTablePageSuspense,
        meta: {title: 'Relevés qualités composants — T-Concept GPAO'},
        name: 'component-reference-values',
        path: '/component-reference-values',
        props: {
            fields: [
                {label: 'Composant', name: 'component', options: {base: 'components'}, sortName: 'component.id', type: 'select'},
                {label: 'Hauteur', name: 'height.value', search: false, sort: false, type: 'measure'},
                {label: 'Tolérance hauteur', name: 'height.tolerance', search: false, sort: false, type: 'measure'},
                {label: 'Largeur', name: 'width.value', search: false, sort: false, type: 'measure'},
                {label: 'Tolérance largeur', name: 'width.tolerance', search: false, sort: false, type: 'measure'},
                {label: 'Section', name: 'section', search: false, sort: false, type: 'measure'},
                {label: 'Tension', name: 'tensile.value', search: false, sort: false, type: 'measure'},
                {label: 'Tolérance tension', name: 'tensile.tolerance', search: false, sort: false, type: 'measure'}
            ],
            icon: 'check-circle',
            sort: readonly({label: 'Nom', name: 'name'}),
            title: 'Relevés qualités composants'
        }
    }
]
