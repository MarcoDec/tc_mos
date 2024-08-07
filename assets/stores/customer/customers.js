import api from '../../api'
import {defineStore} from 'pinia'

export const useCustomersStore = defineStore('customers', {
    actions: {
        async fetch() {
            const response = await api('/api/customers', 'GET')
            this.customers = response['hydra:member']
        }
    },
    getters: {
        customersOption: state => state.customers.map(customer => {
            const opt = {
                text: customer.name,
                value: customer['@id']
            }
            return opt
        })
    },
    state: () => ({
        customers: []
    })
})
