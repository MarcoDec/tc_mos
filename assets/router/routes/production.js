export default [
    {
        component: () => import('../pages/production/engine/AppTablePageEngineGroup.vue'),
        meta: {requiresAuth: true},
        name: 'engine-groups',
        path: '/engine-groups',
        props: {icon: 'wrench', title: 'Groupes d\'équipements'}
    },
    {
        component: async () => import('../pages/production/AppWarehouseList.vue'),
        meta: {requiresAuth: true},
        name: 'warehouse-list',
        path: '/warehouse/list',
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
                    label: 'Famille',
                    name: 'famille',
                    // options: [{text: 'prison', value: 'prison'}, {text: 'production', value: 'production'}, {text: 'réception', value: 'réception'}, {text: 'magasin piéces finies', value: 'magasinPiécesFinies'}, {text: 'expédition', value: 'expédition'}, {text: 'magasin matières premiéres', value: 'magasinMatièresPremiéres'}, {text: 'camion', value: 'camion'}],
                    sort: false,
                    type: 'text',
                    update: true
                }
            ],
            icon: 'warehouse',
            title: 'Entrepots'
        }
    },
    {
        component: async () => import('../pages/production/AppWarehouseShow.vue'),
        meta: {requiresAuth: true},
        name: 'warehouse-show',
        path: '/warehouse/show',
        props: {
            icon: 'warehouse',
            title: 'Entrepots'
        }
    }
]
