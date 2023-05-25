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
                {label: 'Hauteur', name: 'height.value', search: false, sort: false, type: 'measure', measure: {code: 'm', value: 'valeur'}},
                {label: 'Tolérance hauteur', name: 'height.tolerance', search: false, sort: false, type: 'measure', measure: {code: 'm', value: 'valeur'}},
                {label: 'Largeur', name: 'width.value', search: false, sort: false, type: 'measure', measure: {code: 'm', value: 'valeur'}},
                {label: 'Tolérance largeur', name: 'width.tolerance', search: false, sort: false, type: 'measure', measure: {code: 'm', value: 'valeur'}},
                {label: 'Section', name: 'section', search: false, sort: false, type: 'measure', measure: {code: 'mm²', value: 'valeur'}},
                {label: 'Tension', name: 'tensile.value', search: false, sort: false, type: 'measure', measure: {code: 'N', value: 'valeur'}},
                {label: 'Tolérance tension', name: 'tensile.tolerance', search: false, sort: false, type: 'measure', measure: {code: 'N', value: 'valeur'}}
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
