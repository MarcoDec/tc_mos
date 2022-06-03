import {ProductFamilyRepository} from '../../store/modules'

export const familyFields = [
    {label: 'Nom', name: 'name', type: 'text'},
    {label: 'Code douanier', name: 'customsCode', type: 'text'},
    {label: 'Icône', name: 'file', type: 'file'}
]

export default [
    {
        component: async () => import('../pages/AppTreePage'),
        meta: {requiresAuth: true},
        name: 'product-families',
        path: '/product-families',
        props: {
            fields: [
                {label: 'Parent', name: 'parent', repo: ProductFamilyRepository, type: 'select'},
                ...familyFields
            ],
            repo: ProductFamilyRepository,
            title: 'Familles de produits'
        }
    }
]
