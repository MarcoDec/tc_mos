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
    },
    {
        component: async () => import('../pages/project/AppOperationList.vue'),
        meta: {requiresAuth: true},
        name: 'operationList',
        path: '/operation/list',
        props: {
            fields: [
                {ajoutVisible: true, label: 'Code', min: false, name: 'code', trie: true, type: 'text', updateVisible: true},
                {ajoutVisible: false, label: 'Nom', min: false, name: 'name', trie: true, type: 'text', updateVisible: true},
                {ajoutVisible: true, label: 'Type', min: false, name: 'type', trie: true, type: 'number', updateVisible: true},
                {ajoutVisible: false, label: 'Auto', min: false, name: 'auto', trie: true, type: 'boolean', updateVisible: true},
                {ajoutVisible: true, label: 'Limite', min: false, name: 'limite', trie: false, type: 'text', updateVisible: true},
                {ajoutVisible: false, label: 'cadence', min: false, name: 'cadence', trie: true, type: 'number', updateVisible: false},
                {ajoutVisible: true, label: 'Prix', min: false, name: 'prix', trie: true, type: 'number', updateVisible: true},
                {ajoutVisible: true, label: 'Temps(en ms)', min: false, name: 'Temps', trie: false, type: 'date', updateVisible: false}
            ],
            icon: 'atom',
            title: 'Opération'
        }
    },
    {
        component: async () => import('../pages/direction/AppCardableCollectionTable.vue'),
        meta: {requiresAuth: true},
        name: 'societyList',
        path: '/society/list',
        props: {
            fields: [
                {ajoutVisible: true, label: 'Nom', min: true, name: 'name', trie: true, type: 'text', updateVisible: true},
                {ajoutVisible: true, label: 'Adresse', min: false, name: 'adresse', trie: true, type: 'text', updateVisible: true},
                {ajoutVisible: true, label: 'Complément d\'adresse', min: false, name: 'complement', trie: true, type: 'text', updateVisible: true},
                {ajoutVisible: true, label: 'Ville', min: true, name: 'ville', trie: true, type: 'text', updateVisible: true},
                {ajoutVisible: false, label: 'Pays', min: true, name: 'pays', trie: true, type: 'text', updateVisible: true}
            ],
            icon: 'city',
            title: 'Société'
        }
    }
]
