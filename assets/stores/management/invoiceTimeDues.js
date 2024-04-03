import api from '../../api'
import {defineStore} from 'pinia'

export const useInvoiceTimeDuesStore = defineStore('invoiceTimeDues', {
    actions: {
        async fetchInvoiceTime() {
            const response = await api('/api/invoice-time-dues', 'GET')
            this.invoicesData = response['hydra:member']
        },
        async invoiceTimeDuesOption() {
            const response = await api('/api/invoice-time-dues/options', 'GET')
            this.invoicesOption = response['hydra:member']
        }
    },
    getters: {
        invoiceTimeDuesOptions: state => state.invoicesOption.map(invoiceOption => {
            const opt = {
                text: invoiceOption.name,
                value: invoiceOption['@id']
            }
            return opt
        })
    },
    state: () => ({
        invoicesData: [],
        invoicesOption: []
    })
})
