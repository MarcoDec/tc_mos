import {actionsItems, gettersItems} from '../tables/items'
import {defineStore} from 'pinia'
import generateCustomer from './customer'

export default defineStore('customers', {
    actions: {
        ...actionsItems,
        async fetch() {
            const response = [
                {
                    etat: 'refus',
                    id: 1,
                    nom: 'AML'
                },
                {
                    etat: 'attente',
                    id: 2,
                    nom: 'ABB'
                },
                {
                    etat: 'valide',
                    id: 3,
                    nom: 'HUAWEI'
                }
            ]
            for (const customer of response)
                this.items.push(generateCustomer(customer, this))
        }
    },
    getters: {
        ...gettersItems,
        label() {
            return value =>
                this.options.find(option => option.value === value)?.text ?? null
        },
        options: state =>
            state.items
                .map(customer => customer.option)
                .sort((a, b) => a.text.localeCompare(b.text))
    },

    state: () => ({items: []})
})
