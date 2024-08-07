import api from '../../api'
import {defineStore} from 'pinia'
import {useComponentSuppliersPricesStore} from './componentSuppliersPrices'
import useOptions from '../option/options'

export const useComponentSuppliersStore = defineStore('componentSuppliers', {
    actions: {
        async fetch(criteria = '') {
            const response = await api(`/api/supplier-components${criteria}`, 'GET')
            this.items = await response['hydra:member']
        },
        // async fetchByComponent(idComponent){
        //     const response = await api(`/api/supplier-components?component=${idComponent}`, 'GET')
        //     this.items = await response['hydra:member']
        // },
        // async fetchBySupplier(idSupplier){
        //     const response = await api(`/api/supplier-components?supplier=${idSupplier}`, 'GET')
        //     this.items = await response['hydra:member']
        // },
        async fetchPricesById(id) {
            const pricesStore = useComponentSuppliersPricesStore()
            const prices = await pricesStore.fetchPricesByComponent(id)
            return prices
        },
        async fetchPricesForItems() {
            const promises = this.items.map(async item => {
                const prices = await this.fetchPricesById(item['@id'])
                return {...item, prices}
            })
            const itemsWithPrices = await Promise.all(promises)
            this.itemsPrices = itemsWithPrices
            return itemsWithPrices
        },
        async remove(id){
            await api(`/api/supplier-components/${id}`, 'DELETE')
            this.itemsPrices = this.itemsPrices.filter(item => Number(item['@id'].match(/\d+/)[0]) !== id)
        },
        async fetchUnitOptions() {
            const fetchUnitOptions = useOptions('units')
            await fetchUnitOptions.fetchOp()
            this.optionsUnit = fetchUnitOptions.options.map(op => ({
                text: op.text,
                value: op.value
            }))
        },
        async addComponentSuppliers(payload){
            await this.fetchUnitOptions()
            const supplierComponent = {
                code: payload.formData.reference ?? '',
                component: payload.component ?? '',
                copperWeight: {
                    code: this.optionsUnit.find(option => option.value === payload.formData.poidsCu.code ?? null)?.text ?? null,
                    value: payload.formData.poidsCu.value ?? 0
                },
                deliveryTime: {
                    code: this.optionsUnit.find(option => option.value === payload.formData.delai.code ?? null)?.text ?? null,
                    value: payload.formData.delai.value ?? 0
                },
                incoterms: payload.formData.incoterms ?? '',
                moq: {
                    code: this.optionsUnit.find(option => option.value === payload.formData.moq.code ?? null)?.text ?? null,
                    value: payload.formData.moq.value ?? 0
                },
                packaging: {
                    code: this.optionsUnit.find(option => option.value === payload.formData.packaging.code ?? null)?.text ?? null,
                    value: payload.formData.packaging.value ?? 0
                },
                packagingKind: payload.formData.packagingKind ?? '',
                proportion: payload.formData.proportion ?? 0
            }
            this.items = await api('/api/supplier-components', 'POST', supplierComponent)
        },
        async updateComponentSuppliers(payload){
            await this.fetchUnitOptions()
            const supplierComponent = {
                code: payload.reference ?? '',
                copperWeight: {
                    code: this.optionsUnit.find(option => option.value === payload.poidsCu.code ?? null)?.text ?? null,
                    value: payload.poidsCu.value ?? 0
                },
                deliveryTime: {
                    code: this.optionsUnit.find(option => option.value === payload.delai.code ?? null)?.text ?? null,
                    value: payload.delai.value ?? 0
                },
                incoterms: payload.incoterms,
                moq: {
                    code: this.optionsUnit.find(option => option.value === payload.moq.code ?? null)?.text ?? null,
                    value: payload.moq.value ?? 0
                },
                packaging: {
                    code: this.optionsUnit.find(option => option.value === payload.packaging.code ?? null)?.text ?? null,
                    value: payload.packaging.value ?? 0
                },
                packagingKind: payload.packagingKind ?? '',
                proportion: payload.proportion ?? 0
            }
            this.items = await api(`/api/supplier-components/${payload.id}`, 'PATCH', supplierComponent)
        }
    },
    getters: {
        componentSuppliersItems: state => state.itemsPrices.map(item => ({
            '@id': item['@id'],
            id: item.id,
            proportion: item.proportion,
            delai: {
                code: item.deliveryTime.code,
                value: item.deliveryTime.value
            },
            moq: {
                code: item.moq.code,
                value: item.moq.value
            },
            poidsCu: {
                code: item.copperWeight.code,
                value: item.copperWeight.value
            },
            packaging: {
                code: item.packaging.code,
                value: item.packaging.value
            },
            packagingKind: item.packagingKind,
            incoterms: item.incoterms,
            reference: item.code,
            indice: item.index,
            prices: item.prices
        }))
    },
    state: () => ({
        items: [],
        itemsPrices: [],
        optionsUnit: []
    })
})
