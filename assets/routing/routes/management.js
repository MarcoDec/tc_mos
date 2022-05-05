import {
    ColorRepository,
    CountryRepository,
    IncotermsRepository,
    InvoiceTimeDueRepository,
    SocietyRepository,
    UnitRepository,
    VatMessageRepository
} from '../../store/modules'

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
            role: 'isManagementAdmin',
            title: 'Couleurs'
        }
    },
    {
        component: async () => import('../pages/management/AppCurrenciesPage.vue'),
        meta: {requiresAuth: true},
        name: 'currencies',
        path: '/currencies'
    },
    {
        component: async () => import('../pages/AppCollectionTablePage.vue'),
        meta: {requiresAuth: true},
        name: 'invoice-time-dues',
        path: '/invoice-time-dues',
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
                    label: 'Jours',
                    name: 'days',
                    sort: false,
                    type: 'number',
                    update: true
                },
                {
                    create: true,
                    filter: true,
                    label: 'Fin du mois',
                    name: 'endOfMonth',
                    sort: false,
                    type: 'boolean',
                    update: true
                },
                {
                    create: true,
                    filter: true,
                    label: 'Jours après la fin du mois',
                    name: 'daysAfterEndOfMonth',
                    sort: false,
                    type: 'number',
                    update: true
                }
            ],
            icon: 'hourglass-half',
            repo: InvoiceTimeDueRepository,
            role: 'isManagementWriter',
            title: 'Délais de paiement des factures'
        }
    },
    {
        component: async () => import('../pages/AppCardableCollectionTable.vue'),
        meta: {requiresAuth: true},
        name: 'societies',
        path: '/societies',
        props: {
            fields: [
                {
                    collection: true,
                    create: true,
                    filter: true,
                    label: 'Nom',
                    name: 'name',
                    sort: true,
                    type: 'text',
                    update: true
                },
                {
                    collection: false,
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
                    collection: false,
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
                    collection: false,
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
                    collection: false,
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
                    collection: false,
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
                    collection: false,
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
                    collection: false,
                    create: true,
                    filter: true,
                    label: 'E-mail',
                    name: 'address.email',
                    sort: true,
                    sortName: 'address.email',
                    type: 'text',
                    update: true
                },
                {
                    collection: false,
                    create: true,
                    filter: true,
                    label: 'Détails bancaires',
                    name: 'bankDetails',
                    sort: true,
                    type: 'text',
                    update: true
                },
                {
                    collection: false,
                    create: true,
                    filter: true,
                    label: 'Numéro de fax',
                    name: 'fax',
                    sort: true,
                    type: 'text',
                    update: true
                },
                {
                    collection: false,
                    create: true,
                    filter: true,
                    label: 'Forme juridique',
                    name: 'legalForm',
                    sort: true,
                    type: 'text',
                    update: true
                },
                {
                    collection: false,
                    create: true,
                    filter: true,
                    label: 'Notes',
                    name: 'notes',
                    sort: true,
                    type: 'text',
                    update: true
                },
                {
                    collection: false,
                    create: true,
                    filter: true,
                    label: 'SIREN',
                    name: 'siren',
                    sort: true,
                    type: 'text',
                    update: true
                },
                {
                    collection: false,
                    create: true,
                    filter: true,
                    label: 'Site internet',
                    name: 'web',
                    sort: true,
                    type: 'text',
                    update: true
                },
                {
                    collection: false,
                    create: true,
                    filter: true,
                    label: 'Compte de comptabilité',
                    name: 'accountingAccount',
                    sort: true,
                    type: 'text',
                    update: true
                },
                {
                    collection: false,
                    create: true,
                    filter: true,
                    label: 'Accusé de réception',
                    name: 'ar',
                    sort: true,
                    type: 'text',
                    update: true
                },
                {
                    collection: false,
                    create: true,
                    filter: true,
                    label: 'Forcer la TVA',
                    name: 'forceVat',
                    sort: true,
                    type: 'text',
                    update: true
                },
                {
                    collection: false,
                    create: true,
                    filter: true,
                    label: 'Incoterms',
                    name: 'incoterms',
                    repo: IncotermsRepository,
                    sort: true,
                    sortName: 'incoterms.name',
                    type: 'select',
                    update: true
                },
                {
                    collection: false,
                    create: true,
                    filter: true,
                    label: 'Délai de paiement des factures',
                    name: 'invoiceTimeDue',
                    repo: InvoiceTimeDueRepository,
                    sort: true,
                    sortName: 'invoiceTimeDue.name',
                    type: 'select',
                    update: true
                },
                {
                    collection: false,
                    create: true,
                    filter: true,
                    label: 'Taux ppm',
                    name: 'ppmRate',
                    sort: true,
                    type: 'number',
                    update: true
                },
                {
                    collection: false,
                    create: true,
                    filter: true,
                    label: 'TVA',
                    name: 'vat',
                    sort: true,
                    type: 'text',
                    update: true
                },
                {
                    collection: false,
                    create: true,
                    filter: true,
                    label: 'Message TVA',
                    name: 'vatMessage',
                    repo: VatMessageRepository,
                    sort: true,
                    sortName: 'vatMessage.name',
                    type: 'select',
                    update: true
                }
            ],
            icon: 'city',
            repo: SocietyRepository,
            role: 'isManagementReader',
            title: 'Sociétés'
        }
    },
    {
        component: async () => import('../pages/AppCollectionTablePage.vue'),
        meta: {requiresAuth: true},
        name: 'units',
        path: '/units',
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
                    label: 'Base',
                    name: 'base',
                    sort: true,
                    type: 'number',
                    update: true
                },
                {
                    create: true,
                    filter: true,
                    label: 'Parent',
                    name: 'parent',
                    repo: UnitRepository,
                    sort: true,
                    sortName: 'parent.code',
                    type: 'select',
                    update: true
                }
            ],
            icon: 'ruler-horizontal',
            repo: UnitRepository,
            role: 'isManagementAdmin',
            title: 'Unités'
        }
    },
    {
        component: async () => import('../pages/AppCollectionTablePage.vue'),
        meta: {requiresAuth: true},
        name: 'vat-messages',
        path: '/vat-messages',
        props: {
            fields: [{
                create: true,
                filter: true,
                label: 'Message',
                name: 'name',
                sort: true,
                type: 'text',
                update: true
            }],
            icon: 'comments-dollar',
            repo: VatMessageRepository,
            role: 'isManagementAdmin',
            title: 'Messages TVA'
        }
    }
]
