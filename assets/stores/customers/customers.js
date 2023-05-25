import api from '../../api'
import {defineStore} from 'pinia'
import generateCustomer from './customer'

export const useCustomerStore = defineStore('customers', {
    actions: {
        async fetchOne() {
            const response = await api('/api/customers/1', 'GET')
            const item = generateCustomer(response, this)
            this.customer = item
        },
        async fetchInvoiceTime() {
            const response = await api('/api/invoice-time-dues', 'GET')
            this.invoicesData = response['hydra:member']
        },
        async update(data, id) {
            await api(`/api/customers/${id}/logistics`, 'PATCH', data)
            this.fetchOne()
        }
    },
    getters: {
    },
    state: () => ({
        customer: {},
        invoicesData: []

    })
})
