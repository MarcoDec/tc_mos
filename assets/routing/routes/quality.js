import {RejectTypeRepository} from '../../store/modules'

export default [
    {
        component: async () => import('../pages/AppCollectionTablePage.vue'),
        meta: {requiresAuth: true},
        name: 'reject-types',
        path: '/reject-types',
        props: {
            brands: true,
            fields: [
                {
                    create: true,
                    filter: true,
                    label: 'Nom',
                    name: 'name',
                    sort: true,
                    type: 'text',
                    update: true
                }
            ],
            icon: 'elementor',
            repo: RejectTypeRepository,
            title: 'Catégories de rejets de production'
        }
    }
]
