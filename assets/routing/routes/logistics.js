import {CarrierRepository, CountryRepository} from '../../store/modules'

export default [
    {
        component: async () => import('../pages/AppCollectionTablePage.vue'),
        meta: {requiresAuth: true},
        name: 'carriers',
        path: '/carriers',
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
                    label: 'Adresse',
                    name: 'address.address',
                    sort: true,
                    sortName: 'address.address',
                    type: 'text',
                    update: true
                },
                {
                    create: true,
                    filter: true,
                    label: 'Complément d\'adresse',
                    name: 'address.address',
                    sort: true,
                    sortName: 'address.address',
                    type: 'text',
                    update: true
                },
                {
                    create: true,
                    filter: true,
                    label: 'Ville',
                    name: 'address.city',
                    sort: true,
                    sortName: 'address.city',
                    type: 'text',
                    update: true
                },
                {
                    create: true,
                    filter: true,
                    label: 'Code postal',
                    name: 'address.zipCode',
                    sort: true,
                    sortName: 'address.zipCode',
                    type: 'text',
                    update: true
                },
                {
                    create: true,
                    filter: true,
                    label: 'Pays',
                    name: 'address.country',
                    repo: CountryRepository,
                    sort: true,
                    sortName: 'address.country',
                    type: 'select',
                    update: true
                },
                {
                    create: true,
                    filter: true,
                    label: 'Numéro de téléphone',
                    name: 'address.phoneNumber',
                    sort: true,
                    sortName: 'address.phoneNumber',
                    type: 'text',
                    update: true
                },
                {
                    create: true,
                    filter: true,
                    label: 'E-mail',
                    name: 'address.email',
                    sort: true,
                    sortName: 'address.email',
                    type: 'text',
                    update: true
                }
            ],
            icon: 'shuttle-van',
            repo: CarrierRepository,
            title: 'Transporteurs'
        }
    }
]
