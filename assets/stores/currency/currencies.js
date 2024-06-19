import {defineStore} from 'pinia'
import api from '../../api'

export const useCurrenciesStore = defineStore('currencies', {
    actions: {
        async fetch() {
            const response = await api('/api/currencies', 'GET')
            this.currencies = response['hydra:member']
        }
    },
    getters: {},

    state: () => ({
        currencies: []
    })
})
