import api from '../../api'
import {defineStore} from 'pinia'

export const useCustomerOrderStore = defineStore('customerOrder', {
    actions: {
        async fetch(filter='') {
            const response = await api(`/api/selling-orders${filter}`, 'GET')
            this.customerOrders = response['hydra:member']
        },
        async fetchById(id) {
            this.customerOrder = await api(`/api/selling-orders/${id}`, 'GET')
        },
        async updateSellingOrder(payload) {
            await api(`/api/selling-orders/${payload.id}`, 'PATCH', payload.SellingOrder)
            await this.fetch()
        },
        async remove(id) {
            await api(`/api/selling-orders/${id}`, 'DELETE')
            await this.fetch()
        }
    },
    getters: {
        customer: state => state.customerOrder.customer
    },
    state: () => ({
        customerOrders: [],
        customerOrder: {},
        currentPage: 1,
        firstPage: 1,
        nextPage: null,
        pagination: true,
        previousPage: null,
        lastPage: null,
    })
})
