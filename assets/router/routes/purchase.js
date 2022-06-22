import AppShowGui from '../pages/AppShowGui.vue'
import Fields from '../../fields/Fields'

export default [
    {
        component: () => import('../pages/AppTreePage.vue'),
        meta: {requiresAuth: true},
        name: 'component-families',
        path: '/component-families',
        props: {
            fields: new Fields([
                {label: 'Parent', name: 'parent', options: 'component-families', type: 'select'},
                {label: 'Code', name: 'code'},
                {label: 'Nom', name: 'name'},
                {label: 'Cuivre', name: 'copperable', type: 'boolean'},
                {label: 'Code douanier', name: 'customsCode'},
                {label: 'Icône', name: 'file', type: 'file'}
            ]),
            label: 'composants'
        }
    },
    {
        component: AppShowGui,
        meta: {requiresAuth: true},
        name: 'supplier-show',
        path: '/supplier/show'
    }
]
