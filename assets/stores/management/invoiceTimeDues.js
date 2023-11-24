import api from '../../api'
import {defineStore} from 'pinia'

export const useInvoiceTimeDuesStore = defineStore('invoiceTimeDues', {
    actions: {
        async fetchInvoiceTime() {
            const response = await api('/api/invoice-time-dues', 'GET')
            this.invoicesData = response['hydra:member']
        }
    },
    getters: {},
    state: () => ({
        invoicesData: []
    })
})
