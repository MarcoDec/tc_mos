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
