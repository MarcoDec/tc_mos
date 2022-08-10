export default [
    {
        component: async () => import('../pages/AppRowsTablePage.vue'),
        meta: {requiresAuth: true},
        name: 'prices',
        path: '/prices',
        props: {
            fields: [

                {
                    create: false,
                    filter: true,
                    label: 'Nom',
                    name: 'name',
                    prefix: 'componentSuppliers',
                    sort: false,
                    type: 'text',
                    update: true
                },
                {
                    create: true,
                    filter: true,
                    label: 'Proportion',
                    name: 'proportion',
                    prefix: 'componentSuppliers',
                    sort: false,
                    type: 'text',
                    update: true
                },
                {
                    create: true,
                    filter: true,
                    label: 'Délai',
                    name: 'delai',
                    prefix: 'componentSuppliers',
                    sort: false,
                    type: 'number',
                    update: true
                },
                {
                    create: false,
                    filter: true,
                    label: 'Moq',
                    name: 'moq',
                    prefix: 'componentSuppliers',
                    sort: false,
                    type: 'number',
                    update: true
                },
                {
                    create: false,
                    filter: true,
                    label: 'Poids cu',
                    name: 'poidsCu',
                    prefix: 'componentSuppliers',
                    sort: false,
                    type: 'text',
                    update: true
                },
                {
                    create: false,
                    filter: true,
                    label: 'Référence',
                    name: 'reference',
                    prefix: 'componentSuppliers',
                    sort: false,
                    type: 'text',
                    update: true
                },
                {
                    create: true,
                    filter: true,
                    label: 'Indice',
                    name: 'indice',
                    prefix: 'componentSuppliers',
                    sort: false,
                    type: 'number',
                    update: true
                },
                {
                    children: [
                        {create: true, filter: true, label: '€', name: 'price', prefix: 'componentSupplierPrices', sort: false, type: 'number', update: true},
                        {create: true, filter: true, label: 'Q', name: 'quantite', prefix: 'componentSupplierPrices', sort: false, type: 'number', update: true},
                        {create: true, filter: true, label: 'ref', name: 'ref', prefix: 'componentSupplierPrices', sort: false, type: 'number', update: true}
                    ],
                    create: false,
                    filter: true,
                    label: 'Prix',
                    name: 'prices',
                    prefix: 'componentSuppliers',
                    sort: false,
                    type: 'text',
                    update: true
                }
            ]
        }
    }
]