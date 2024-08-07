import api from '../../api'
import {defineStore} from 'pinia'
import useOptions from '../option/options'
import {useCurrenciesStore} from '../currencies/currencies'

export const useComponentSuppliersPricesStore = defineStore('componentSuppliersPrices', {
    actions: {
        async fetchPricesByComponent(idComponent){
            const response = await api(`/api/supplier-component-prices?component=${idComponent}`, 'GET')
            this.itemPricesComponent = await response['hydra:member']
            return this.itemPricesComponent
        },
        async removePrice(id){
            await api(`/api/supplier-component-prices/${id}`, 'DELETE')
            this.itemPricesComponent = this.itemPricesComponent.filter(itemPriceComponent => Number(itemPriceComponent['@id'].match(/\d+/)[0]) !== id)
        },
        async fetchUnitOptions() {
            const fetchUnitOptions = useOptions('units')
            await fetchUnitOptions.fetchOp()
            this.optionsUnit = fetchUnitOptions.options.map(op => ({
                text: op.text,
                value: op.value
            }))
        },
        async fetchCurrencyOptions() {
            const storeCurrencies = useCurrenciesStore()
            await storeCurrencies.fetch()
            const activeCurrencies = storeCurrencies.currencies.filter(currency => currency.active === true)
            this.currenciesOption = activeCurrencies.map(currency => ({
                text: currency.code,
                value: currency['@id']
            }))
        },
        async addPrices(payload){
            await this.fetchUnitOptions()
            await this.fetchCurrencyOptions()

            const price = {
                component: payload.component,
                price: {
                    code: this.currenciesOption.find(currency => currency.value === payload.formData.price.code ?? null)?.text ?? null,
                    value: payload.formData.price.value ?? 0
                },
                quantity: {
                    code: this.optionsUnit.find(option => option.value === payload.formData.quantity.code ?? null)?.text ?? null,
                    value: payload.formData.quantity.value ?? 0
                },
                ref: payload.formData.ref ?? ''
            }
            this.itemPricesComponent = await api('/api/supplier-component-prices', 'POST', price)
        },

        async updatePrices(payload){
            await this.fetchUnitOptions()
            await this.fetchCurrencyOptions()

            const price = {
                component: payload.component,
                price: {
                    code: this.currenciesOption.find(currency => currency.value === payload.price.code ?? null)?.text ?? null,
                    value: payload.price.value ?? 0
                },
                quantity: {
                    code: this.optionsUnit.find(option => option.value === payload.quantity.code ?? null)?.text ?? null,
                    value: payload.quantity.value ?? 0
                },
                ref: payload.ref ?? ''
            }
            this.itemPricesComponent = await api(`/api/supplier-component-prices/${payload.id}`, 'PATCH', price)
        }
    },
    getters: {
    },
    state: () => ({
        itemPricesComponent: [],
        optionsUnit: [],
        currenciesOption: []
    })
})
