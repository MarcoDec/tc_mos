import api from '../../api'
import {defineStore} from 'pinia'

export const useCustomerOrderStore = defineStore('customerOrder', {
    actions: {
        async fetch() {
            const response = await api('/api/selling-orders', 'GET')
            this.customerOrders = response['hydra:member']
        },
        async fetchById(id) {
            const response = await api(`/api/selling-orders/${id}`, 'GET')
            this.customerOrder = response
        },
        async updateSellingOrder(payload) {
            await api(`/api/selling-orders/${payload.id}`, 'PATCH', payload.SellingOrder)
            this.fetch()
        }
    },
    getters: {
        customer: state => state.customerOrder.customer
    },
    state: () => ({
        customerOrders: [],
        customerOrder: []
    })
})
