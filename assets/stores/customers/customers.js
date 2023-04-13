import api from '../../api'
import {defineStore} from 'pinia'
import generateCustomer from './customer'

export const useCustomerStore = defineStore('customer', {
    actions: {
        async fetch() {
            const response = await api('/api/customers/1', 'GET')
            const item = generateCustomer(response, this)
            this.customer = item
        }

    },
    getters: {
    },
    state: () => ({
        customer: {}

    })
})
