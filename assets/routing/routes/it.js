export default [
    {
        component: async () => import('../pages/AppCollectionTablePage.vue'),
        meta: {requiresAuth: true},
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
    }
]
