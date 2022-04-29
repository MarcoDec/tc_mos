import {CountryRepository, EventTypeRepository, OutTrainerRepository, TimeSlotRepository} from '../../store/modules'

export default [
    {
        component: async () => import('../pages/AppCollectionTablePage.vue'),
        meta: {requiresAuth: true},
        name: 'event-types',
        path: '/event-types',
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
            repo: EventTypeRepository,
            role: 'isHrAdmin',
            title: 'Catégories d\'événements des employés'
        }
    },
    {
        component: async () => import('../pages/AppCollectionTablePage.vue'),
        meta: {requiresAuth: true},
        name: 'out-trainers',
        path: '/out-trainers',
        props: {
            fields: [
                {
                    create: true,
                    filter: true,
                    label: 'Nom',
                    name: 'surname',
                    sort: true,
                    type: 'text',
                    update: true
                },
                {
                    create: true,
                    filter: true,
                    label: 'Prénom',
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
                    name: 'address.address2',
                    sort: true,
                    sortName: 'address.address2',
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
            icon: 'user-graduate',
            repo: OutTrainerRepository,
            role: 'isHrWriter',
            title: 'Formateurs extérieurs'
        }
    },
    {
        component: async () => import('../pages/AppCollectionTablePage.vue'),
        meta: {requiresAuth: true},
        name: 'time-slots',
        path: '/time-slots',
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
                    label: 'Début',
                    name: 'start',
                    sort: true,
                    type: 'time',
                    update: true
                },
                {
                    create: true,
                    filter: true,
                    label: 'Début de la pause',
                    name: 'startBreak',
                    sort: true,
                    type: 'time',
                    update: true
                },
                {
                    create: true,
                    filter: true,
                    label: 'Fin de la pause',
                    name: 'endBreak',
                    sort: true,
                    type: 'time',
                    update: true
                },
                {
                    create: true,
                    filter: true,
                    label: 'Fin',
                    name: 'end',
                    sort: true,
                    type: 'time',
                    update: true
                }
            ],
            icon: 'clock',
            repo: TimeSlotRepository,
            role: 'isHrAdmin',
            title: 'Plages horaires'
        }
    }
]
