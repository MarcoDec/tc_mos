import api from '../../api'
import {defineStore} from 'pinia'
import generateSupplier from './supplier'

export const useSuppliersStore = defineStore('suppliers', {
    actions: {
        async fetchOne(id = 1) {
            const response = await api(`/api/suppliers/${id}`, 'GET')
            const item = generateSupplier(response, this)
            this.supplier = item
        },
        async fetchVatMessage() {
            const response = await api('/api/vat-messages', 'GET')
            this.vatMessage = response['hydra:member']
        }

    },
    getters: {
    },
    state: () => ({
        supplier: {},
        suppliers: {},
        vatMessage: []
    })
})
