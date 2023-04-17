import api from '../../api'
import {defineStore} from 'pinia'
import generateCustomer from './customer'

export const useCustomerStore = defineStore('customers', {
    actions: {
        async fetch() {
            const response = await api('/api/customers/1', 'GET')
            const item = generateCustomer(response, this)
            this.customer = item
            console.log('res', response)
        },
        async fetchInvoiceTime() {
            const response = await api('/api/invoice-time-dues', 'GET')
            this.invoicesData = response['hydra:member']
            console.log('res Invoice Time', response['hydra:member'])
        },
        async update(data, id) {
            const response = await api(`/api/customers/${id}/logistics`, 'PATCH', data)
            this.fetch()
        },
        async updateSociety(data, id) {
            const response = await api(`/api/societies/${id}`, 'PATCH', data)
            this.fetch()
            console.log('update Society***',response );
        },
    },
    getters: {
    },
    state: () => ({
        customer: {},
        invoicesData: []

    })
})
