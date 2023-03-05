import api from '../../api'
import {defineStore} from 'pinia'
import generateCountry from './country'

export default defineStore('countries', {
    actions: {
        async fetch() {
            this.$reset()
            const response = await api('/api/countries')
            for (const country of response.content['hydra:member'])
                this.items.push(generateCountry(country, this))
        }
    },
    getters: {
        options: state =>
            state.items
                .map(country => country.option)
                .sort((a, b) => a.text.localeCompare(b.text)),

        phoneLabel() {
            return code =>
                this.options
                    .map(country => country.value)
                    .find(value => value === code) ?? null
        }

    },
    state: () => ({items: []})
})
