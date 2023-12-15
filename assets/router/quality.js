import AppTablePageSuspense from '../components/pages/table/AppTablePageSuspense.vue'
import {readonly} from 'vue'

export default [
    {
        component: AppTablePageSuspense,
        meta: {title: 'Relevés qualités composants — T-Concept GPAO'},
        name: 'component-reference-values',
        path: '/component-reference-values',
        props: {
            apiBaseRoute: 'component-reference-values',
            fields: [
                {
                    label: 'Composant',
                    name: 'component',
                    options: {base: 'components'},
                    sortName: 'component.id',
                    type: 'select'
                },
                {label: 'Hauteur', measure: {code: 'm', value: 'valeur'}, name: 'height.value', search: false, sort: false, type: 'measure'},
                {label: 'Tolérance hauteur', measure: {code: 'm', value: 'valeur'}, name: 'height.tolerance', search: false, sort: false, type: 'measure'},
                {label: 'Largeur', measure: {code: 'm', value: 'valeur'}, name: 'width.value', search: false, sort: false, type: 'measure'},
                {label: 'Tolérance largeur', measure: {code: 'm', value: 'valeur'}, name: 'width.tolerance', search: false, sort: false, type: 'measure'},
                {label: 'Section', measure: {code: 'mm²', value: 'valeur'}, name: 'section', search: false, sort: false, type: 'measure'},
                {label: 'Tension', measure: {code: 'N', value: 'valeur'}, name: 'tensile.value', search: false, sort: false, type: 'measure'},
                {label: 'Tolérance tension', measure: {code: 'N', value: 'valeur'}, name: 'tensile.tolerance', search: false, sort: false, type: 'measure'}
            ],
            icon: 'check-circle',
            sort: readonly({label: 'Nom', name: 'name'}),
            title: 'Relevés qualités composants'
        }
    },
    {
        component: AppTablePageSuspense,
        meta: {title: 'Catégories de rejets de production — T-Concept GPAO'},
        name: 'reject-types',
        path: '/reject-types',
        props: {
            apiBaseRoute: 'reject-types',
            brands: true,
            fields: [{label: 'Nom', name: 'name'}],
            icon: 'elementor',
            sort: readonly({label: 'Nom', name: 'name'}),
            title: 'Catégories de rejets de production'
        }
    },
    {
        component: AppTablePageSuspense,
        meta: {title: 'Critères qualités — T-Concept GPAO'},
        name: 'quality-types',
        path: '/quality-types',
        props: {
            apiBaseRoute: 'quality-types',
            brands: true,
            fields: [{label: 'Nom', name: 'name'}],
            icon: 'elementor',
            sort: readonly({label: 'Nom', name: 'name'}),
            title: 'Critères qualités'
        }
    }
]
