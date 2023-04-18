import api from '../../api'
import {defineStore} from 'pinia'
import generateSupplier from './supplier'

export const useSuppliersStore = defineStore('suppliers', {
    actions: {
        async fetch() {
            const response = await api('/api/suppliers/1', 'GET')
            const item = generateSupplier(response, this)
            this.suppliers = item
        },
        async fetchVatMessage() {
            const response = await api('/api/vat-messages', 'GET')
            this.vatMessage = response['hydra:member']
        }

    },
    getters: {
    },
    state: () => ({
        suppliers: {},
        vatMessage: []
    })
})
