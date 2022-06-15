import {ComponentFamilyRepository} from '../../store/modules'
import {familyFields} from './project'

export default [
    {
        component: async () => import('../pages/AppTreePage'),
        meta: {requiresAuth: true},
        name: 'component-families',
        path: '/component-families',
        props: {
            fields: [
                {label: 'Parent', name: 'parent', repo: ComponentFamilyRepository, type: 'select'},
                {label: 'Code', name: 'code', type: 'text'},
                ...familyFields,
                {label: 'Cuivre', name: 'copperable', type: 'boolean'}
            ],
            repo: ComponentFamilyRepository,
            title: 'Familles de composants'
        }
    },
    {
        component: async () => import('../pages/AppShowGuiPage.vue'),
        meta: {requiresAuth: true},
        name: 'supplier-show',
        path: '/supplier/show'
    }
]
