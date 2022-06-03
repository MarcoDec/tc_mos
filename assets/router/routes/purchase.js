export default [
    {
        component: () => import('../pages/AppTreePage.vue'),
        meta: {requiresAuth: true},
        name: 'component-families',
        path: '/component-families',
        props: {
            fields: [
                {label: 'Code', name: 'code'},
                {label: 'Nom', name: 'name'},
                {label: 'Cuivre', name: 'copperable', type: 'boolean'},
                {label: 'Code douanier', name: 'customsCode'},
                {label: 'Icône', name: 'file', type: 'file'}
            ],
            label: 'composants'
        }
    }
]
