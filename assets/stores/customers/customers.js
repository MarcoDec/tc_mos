import api from '../../api'
import {defineStore} from 'pinia'
import generateCustomer from './customer'

export const useCustomerStore = defineStore('customers', {
    actions: {
        async fetchInvoiceTime() {
            const response = await api('/api/invoice-time-dues', 'GET')
            this.invoicesData = response['hydra:member']
        },
        async fetchOne(id = 1) {
            const response = await api(`/api/customers/${id}`, 'GET')
            const item = generateCustomer(response, this)
            this.customer = item
            this.isLoaded = true
        },
        async update(data, id) {
            await api(`/api/customers/${id}/logistics`, 'PATCH', data)
            this.fetchOne(id)
        }
    },
    getters: {
    },
    state: () => ({
        customer: {},
        invoicesData: [],
        isLoaded: false
    })
})
