import {GroupRepository} from '../../store/modules'
import {engineGroupOptions} from '../../utils'

export default [
    {
        component: async () => import('../pages/AppCollectionTablePage.vue'),
        meta: {requiresAuth: true},
        name: 'engine-groups',
        path: '/engine-groups',
        props: {
            fields: [
                {
                    create: true,
                    filter: true,
                    label: 'Code',
                    name: 'code',
                    sort: true,
                    type: 'text',
                    update: true
                },
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
                    label: 'Type',
                    name: '@type',
                    options: engineGroupOptions(),
                    sort: false,
                    type: 'select',
                    update: false
                }
            ],
            icon: 'wrench',
            repo: GroupRepository,
            title: 'Groupes d\'équipements'
        }
    }
]
