export default [
    {
        component: () => import('../pages/AppTreePage.vue'),
        meta: {requiresAuth: true},
        name: 'product-families',
        path: '/product-families',
        props: {
            fields: [
                {label: 'Nom', name: 'name'},
                {label: 'Code douanier', name: 'customsCode'},
                {label: 'Icône', name: 'file', type: 'file'}
            ],
            label: 'produits'
        }
    }
]
