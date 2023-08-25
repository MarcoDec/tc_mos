import api from '../../api'
import {actionsItems, gettersItems} from '../tables/items'
import {defineStore} from 'pinia'
import generateComponent from './component'

export default defineStore('components', {
    actions: {
        ...actionsItems,
        async fetch() {
            const response = [
                {
                    designation: 'des',
                    etat: 'refus',
                    famille: 'cc',
                    fournisseurs: 'cc',
                    id: 1,
                    img: 'ddd',
                    indice: 'CAB-1000',
                    nom: '3M FRANCE',
                    ref: 'CAB-1000'
                },
                {
                    designation: 'dess',
                    etat: 'attente',
                    famille: 'cc',
                    fournisseurs: 'cc',
                    id: 2,
                    img: 'ddd',
                    indice: 'CAB-1000',
                    nom: 'ABB',
                    ref: 'CAB-1000'
                }
            ]
            for (const component of response)
                this.items.push(generateComponent(component, this))
        },
        async addComponent(payload){
            this.component = await api('/api/components', 'POST', payload)
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
                .map(component => component.option)
                .sort((a, b) => a.text.localeCompare(b.text))
    },

    state: () => ({items: [], component: []})
})
