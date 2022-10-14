import AppTablePageSuspense from '../components/pages/table/AppTablePageSuspense.vue'
import Fields from '../utils/Fields'
import {readonly} from 'vue'

const name = {label: 'Nom', name: 'name'}
const readonlyName = {...name, create: false, search: false, sort: false, update: false}

export default [
    {
        component: AppTablePageSuspense,
        meta: {title: 'Couleurs — T-Concept GPAO'},
        name: 'colors',
        path: '/colors',
        props: {
            fields: Fields.generate([{...name}, {label: 'RGB', name: 'rgb', type: 'color'}]),
            icon: 'palette',
            sort: readonly({...name}),
            title: 'Couleurs'
        }
    },
    {
        component: AppTablePageSuspense,
        meta: {title: 'Délais de paiement des factures — T-Concept GPAO'},
        name: 'invoice-time-dues',
        path: '/invoice-time-dues',
        props: {
            fields: Fields.generate([
                {...name},
                {label: 'Jours', name: 'days', sort: false, type: 'number'},
                {label: 'Fin du mois', name: 'endOfMonth', sort: false, type: 'boolean'},
                {label: 'Jours après la fin du mois', name: 'daysAfterEndOfMonth', sort: false, type: 'number'}
            ]),
            icon: 'hourglass-half',
            sort: readonly({...name}),
            title: 'Délais de paiement des factures'
        }
    },
    {
        component: AppTablePageSuspense,
        meta: {title: 'Imprimantes — T-Concept GPAO'},
        name: 'printers',
        path: '/printers',
        props: {
            disableRemove: true,
            fields: Fields.generate([
                readonlyName,
                {create: false, label: 'IP', name: 'ip', search: false, sort: false, update: false},
                {
                    create: false,
                    hideLabelValue: true,
                    label: 'Couleur',
                    name: 'color',
                    options: [{text: '#00cc00', value: 'green'}, {text: '#ffff33', value: 'yellow'}],
                    search: false,
                    sort: false,
                    type: 'color',
                    update: false
                },
                {create: false, label: 'Compagnie', name: 'company.name', search: false, sort: false, update: false}
            ]),
            icon: 'print',
            sort: readonly(readonlyName),
            title: 'Imprimantes'
        }
    },
    {
        component: AppTablePageSuspense,
        meta: {title: 'Messages TVA — T-Concept GPAO'},
        name: 'vat-messages',
        path: '/vat-messages',
        props: {
            fields: Fields.generate([{...name}]),
            icon: 'comments-dollar',
            sort: readonly({...name}),
            title: 'Messages TVA'
        }
    }
]
