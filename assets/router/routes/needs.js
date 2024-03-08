export default [
    {
        component: () => import('../pages/needs/AppNeedsPage.vue'),
        meta: { requiresAuth: true },
        name: 'needs',
        path: '/needs',
        children: [
            {
                path: 'components',
                component: () => import('../pages/needs/AppNeedsPage.vue'),
                props: {type: 'components'} // Ajoutez une propriété 'type' pour distinguer les composants des produits
            },
            {
                path: 'products',
                component: () => import('../pages/needs/AppNeedsPage.vue'),
                props: {type: 'products'} // Ajoutez une propriété 'type' pour distinguer les produits des composants
            }
        ]
    }
]
