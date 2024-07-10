import api from '../../../api'
import {defineStore} from 'pinia'
import generateCustomer from './customer'

export const useCustomerStore = defineStore('customers', {
    actions: {
        async fetchOne(id = 1) {
            const response = await api(`/api/customers/${id}`, 'GET')
            const item = generateCustomer(response, this)
            this.customer = item
            this.isLoaded = true
        },
        async update(data, process) {
            await api(`/api/customers/${this.customer.id}/${process}`, 'PATCH', data)
            this.isLoaded = false
            await this.fetchOne(this.customer.id)
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
