import {actionsItems, gettersItems} from '../tables/items'
import {defineStore} from 'pinia'
import generateSupplier from './supplier'

export default defineStore('suppliers', {
    actions: {
        ...actionsItems,
        async fetch() {
            const response = [
                {
                    etat: 'refus',
                    id: 1,
                    nom: '3M FRANCE'

                },
                {
                    etat: 'attente',
                    id: 2,
                    nom: 'ABB'

                },
                {
                    etat: 'valide',
                    id: 3,
                    nom: 'CD'

                }
            ]
            for (const supplier of response)
                this.items.push(generateSupplier(supplier, this))
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
                .map(supplier => supplier.option)
                .sort((a, b) => a.text.localeCompare(b.text))
    },

    state: () => ({items: []})
})
