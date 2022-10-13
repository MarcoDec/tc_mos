import AppTablePageSuspense from '../components/pages/table/AppTablePageSuspense.vue'
import Fields from '../utils/Fields'
import {readonly} from 'vue'

const name = {label: 'Nom', name: 'name'}

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
