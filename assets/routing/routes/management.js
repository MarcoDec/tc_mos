import {ColorRepository} from '../../store/modules'

export default [
    {
        component: async () => import('../pages/AppCollectionTablePage.vue'),
        meta: {requiresAuth: true},
        name: 'colors',
        path: '/colors',
        props: {
            fields: [
                {
                    create: true,
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
                    label: 'RGB',
                    name: 'rgb',
                    sort: true,
                    type: 'color',
                    update: true
                }
            ],
            icon: 'palette',
            repo: ColorRepository,
            title: 'Couleur'
        }
    }
]
