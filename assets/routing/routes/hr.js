export default [
    {
        component: async () => import('../pages/app-collection-table/AppCollectionTableWrapper.vue'),
        meta: {requiresAuth: true},
        name: 'OutTrainer-list',
        path: '/OutTrainer/list',
        props: {
            fields: [
                {
                    create: false,
                    filter: true,
                    label: 'Nom',
                    name: 'name',
                    sort: true,
                    type: 'text',
                    update: true
                },
                {
                    create: false,
                    filter: true,
                    label: 'Prénom',
                    name: 'prenom',
                    sort: true,
                    type: 'text',
                    update: true
                },
                {
                    create: false,
                    filter: true,
                    label: 'Adresse',
                    name: 'adresse',
                    sort: true,
                    type: 'text',
                    update: true
                },
                {
                    create: false,
                    filter: true,
                    label: 'Complément d adresse',
                    name: 'complementAdresse',
                    sort: true,
                    type: 'text',
                    update: true
                },
                {
                    create: false,
                    filter: true,
                    label: 'Code postal',
                    name: 'codePostal',
                    sort: true,
                    type: 'text',
                    update: true
                },
                {
                    create: false,
                    filter: true,
                    label: 'Ville',
                    name: 'ville',
                    sort: true,
                    type: 'text',
                    update: true
                },
                {
                    create: false,
                    filter: true,
                    label: 'Pays',
                    name: 'pays',
                    options: [{text: 'aaaaa', value: 'aaaaa'}, {text: 'bbbb', value: 'bbbb'}],
                    sort: false,
                    type: 'select',
                    update: true
                },
                {
                    create: false,
                    filter: true,
                    label: 'Téléphone',
                    name: 'telephone',
                    sort: true,
                    type: 'text',
                    update: true
                },
                {
                    create: false,
                    filter: true,
                    label: 'E-mail',
                    name: 'email',
                    sort: true,
                    type: 'text',
                    update: true
                }
            ],
            icon: 'user-graduate',
            title: 'Formateur extérieur'
        }
    },
    {
        component: async () => import('../pages/app-collection-table/AppCollectionTableWrapper.vue'),
        meta: {requiresAuth: true},
        name: 'EmployeeEventType-list',
        path: '/EmployeeEventType/list',
        props: {
            fields: [
                {
                    create: false,
                    filter: true,
                    label: 'Nom',
                    name: 'name',
                    sort: true,
                    type: 'text',
                    update: true
                },
                {
                    create: false,
                    filter: true,
                    label: 'Vers le statut',
                    name: 'versLeStatut',
                    sort: true,
                    type: 'text',
                    update: true
                }
            ],
            icon: 'elementor',
            title: 'Catégories d événement des employés'
        }
    },
    {
        component: async () => import('../pages/app-collection-table/AppCollectionTableWrapper.vue'),
        meta: {requiresAuth: true},
        name: 'TimeSlot-list',
        path: '/TimeSlot/list',
        props: {
            fields: [
                {
                    create: false,
                    filter: true,
                    label: 'Nom',
                    name: 'name',
                    sort: true,
                    type: 'text',
                    update: true
                },
                {
                    create: false,
                    filter: true,
                    label: 'Début',
                    name: 'debut',
                    sort: true,
                    type: 'time',
                    update: true
                },
                {
                    create: false,
                    filter: true,
                    label: 'Début de la pause',
                    name: 'debutPause',
                    sort: true,
                    type: 'time',
                    update: true
                },
                {
                    create: false,
                    filter: true,
                    label: 'Fin de la pause',
                    name: 'finPause',
                    sort: true,
                    type: 'time',
                    update: true
                },
                {
                    create: false,
                    filter: true,
                    label: 'Fin',
                    name: 'fin',
                    sort: true,
                    type: 'time',
                    update: true
                }
            ],
            icon: 'clock',
            title: 'Plage horaire'
        }
    }
]
