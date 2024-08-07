import {defineStore} from 'pinia'
import generateCompagnie from './compagnie'

export default defineStore('compagnies', {
    actions: {
        async fetch() {
            const response = [
                {
                    id: 1,
                    name: 'TCONCEPT'
                },
                {
                    id: 2,
                    name: 'TUNISIETCONCEPT'
                },
                {
                    id: 3,
                    name: 'MG2C'
                }
            ]
            for (const compagnie of response)
                this.items.push(generateCompagnie(compagnie, this))
        }
    },
    getters: {
        options: state =>
            state.items
                .map(compagnie => compagnie.option)
                .sort((a, b) => a.text.localeCompare(b.text)),

        phoneLabel() {
            return code =>
                this.options
                    .map(compagnie => compagnie.value)
                    .find(value => value === code) ?? null
        }
    },
    state: () => ({items: []})
})
