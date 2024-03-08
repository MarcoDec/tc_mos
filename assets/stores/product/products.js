import {actionsItems, gettersItems} from '../tables/items'
import {defineStore} from 'pinia'
import generateProduct from './product'

export default defineStore('products', {
    actions: {
        ...actionsItems,
        async fetch() {
            const response = [
                {
                    besoins: '',
                    client: 'RENAULT',
                    date: 'string',
                    etat: '',
                    famille: 'Cables',
                    id: 1,
                    img: 'string',
                    indice: 'rrrr',
                    ref: '1176481X',
                    show: true,
                    stock: 'ddd'
                },
                {
                    besoins: '',
                    client: 'DACIA',
                    date: 'string',
                    etat: 'valide',
                    famille: 'Cables',
                    id: 2,
                    img: 'string',
                    indice: 'rrrr',
                    ref: '122508040X',
                    show: true,
                    stock: 'ddd'
                },
                {
                    besoins: '',
                    client: 'DACIA',
                    date: 'string',
                    etat: '',
                    famille: 'Cables',
                    id: 3,
                    img: 'string',
                    indice: 'rrrr',
                    ref: '122508040X',
                    show: true,
                    stock: 'ddd'
                }
            ]
            for (const product of response)
                this.items.push(generateProduct(product, this))
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
                .map(product => product.option)
                .sort((a, b) => a.text.localeCompare(b.text))
    },

    state: () => ({items: []})
})
