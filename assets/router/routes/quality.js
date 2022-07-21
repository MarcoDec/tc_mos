import AppTablePage from '../pages/AppTablePage'

export default [
    {
        component: AppTablePage,
        meta: {requiresAuth: true},
        name: 'reject-types',
        path: '/reject-types',
        props: {
            brands: true,
            fields: [{create: true, label: 'Nom', name: 'name', search: true, sort: true, update: true}],
            icon: 'elementor',
            title: 'Catégories de rejets de production'
        }
    },
    {
        component: AppTablePage,
        meta: {requiresAuth: true},
        name: 'quality-types',
        path: '/quality-types',
        props: {
            brands: true,
            fields: [{create: true, label: 'Nom', name: 'name', search: true, sort: true, update: true}],
            icon: 'elementor',
            title: 'Critères qualités'
        }
    }
]
