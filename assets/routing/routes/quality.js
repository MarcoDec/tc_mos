export default [
    {
        component: async () => import('../pages/app-collection-table/AppCollectionTablePage.vue'),
        meta: {requiresAuth: true},
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
        component: async () => import('../pages/app-collection-table/AppCollectionTablePage.vue'),
        meta: {requiresAuth: true},
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
        component: async () => import('../pages/app-collection-table/AppCollectionTablePage.vue'),
        meta: {requiresAuth: true},
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
    }
]
