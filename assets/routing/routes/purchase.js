export default [
    {
        component: async () => import('../pages/app-collection-table/AppCollectionTablePage.vue'),
        meta: {requiresAuth: true},
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
            title: 'Attributs'
        }
    },
    {
        component: async () => import('../pages/tree/AppTreePageWrapper.vue'),
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
    }
]
