import api from '../../api'
import {defineStore} from 'pinia'

export const useCurrenciesStore = defineStore('currencies', {
    actions: {
        async fetch() {
            const response = await api('/api/currencies?pagination=false')
            this.currencies = response['hydra:member']
        }
    },
    getters: {
        currenciesOption: state => {
            const activeCurrencies = state.currencies.filter(currency => currency.active === true)
            return activeCurrencies.map(currency => ({
                text: currency.code,
                value: currency['@id'],
                symbol: currency.symbol
            }))
        }
    },
    state: () => ({
        currencies: []
    })
})
