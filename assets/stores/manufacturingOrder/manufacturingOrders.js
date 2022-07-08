import {actionsItems, gettersItems} from '../tables/items'
import {defineStore} from 'pinia'
import generateManufacturingOrders from './manufacturingOrder'

export default defineStore('manufacturingOrders', {
    actions: {
        ...actionsItems,
        async fetch() {
            const response = [
                {
                    commande: 'cc',
                    compagnie: 'dddd',
                    etat: 'refus',
                    id: 1,
                    indice: 'CAB-1000',
                    numero: '3M FRANCE',
                    produit: 'CAB-1000',
                    quantite: 'cc',
                    quantiteF: 'cc',
                    show: true,
                    usine: 'CAB-1000'
                },
                {
                    commande: 'cc',
                    compagnie: 'dddd',
                    etat: 'refus',
                    id: 2,
                    indice: 'CAB-1000',
                    numero: '3M FRANCE',
                    produit: 'CAB-1000',
                    quantite: 'cc',
                    quantiteF: 'cc',
                    show: true,
                    usine: 'CAB-1000'
                },
                {
                    commande: 'cc',
                    compagnie: 'dddd',
                    etat: 'refus',
                    id: 3,
                    indice: 'CAB-1000',
                    numero: '3M FRANCE',
                    produit: 'CAB-1000',
                    quantite: 'cc',
                    quantiteF: 'cc',
                    show: true,
                    usine: 'CAB-1000'
                }
            ]
            for (const order of response)
                this.items.push(generateManufacturingOrders(order, this))
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
                .map(order => order.option)
                .sort((a, b) => a.text.localeCompare(b.text))
    },

    state: () => ({items: []})
})
