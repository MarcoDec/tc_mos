import api from '../../api'
import {defineStore} from 'pinia'

export const useCustomerOrderStore = defineStore('customerOrder', {
    actions: {
        async addCustomerOrder(payload) {
            await api('/api/selling-orders', 'POST', payload)
        },
        async fetch(filter = '') {
            const response = await api(`/api/selling-orders${filter}`, 'GET')
            this.customerOrders = response['hydra:member']
            this.customerOrders.forEach(order => {
                order.state = order.embState.state
                order.closer = order.embBlocker.state
            })
            this.updatePagination(response)
        },
        async fetchById(id) {
            this.customerOrder = await api(`/api/selling-orders/${id}`, 'GET')
            this.customerOrder.state = this.customerOrder.embState.state
            this.customerOrder.closer = this.customerOrder.embBlocker.state
        },
        async updateSellingOrder(payload) {
            console.log(payload)
            await api(`/api/selling-orders/${payload.id}`, 'PATCH', payload.SellingOrder)
            await this.fetch()
        },
        async remove(id) {
            await api(`/api/selling-orders/${id}`, 'DELETE')
            await this.fetch()
        },
        updatePagination(response) {
            const responseData = response['hydra:member']
            let paginationView = {}
            if (Object.prototype.hasOwnProperty.call(response, 'hydra:view')) {
                paginationView = response['hydra:view']
            } else {
                paginationView = responseData
            }
            if (Object.prototype.hasOwnProperty.call(paginationView, 'hydra:first')) {
                this.pagination = true
                this.firstPage = paginationView['hydra:first'] ? paginationView['hydra:first'].match(/page=(\d+)/)[1] : '1'
                this.lastPage = paginationView['hydra:last'] ? paginationView['hydra:last'].match(/page=(\d+)/)[1] : paginationView['@id'].match(/page=(\d+)/)[1]
                this.nextPage = paginationView['hydra:next'] ? paginationView['hydra:next'].match(/page=(\d+)/)[1] : paginationView['@id'].match(/page=(\d+)/)[1]
                this.currentPage = paginationView['@id'].match(/page=(\d+)/)[1]
                this.previousPage = paginationView['hydra:previous'] ? paginationView['hydra:previous'].match(/page=(\d+)/)[1] : paginationView['@id'].match(/page=(\d+)/)[1]
                return responseData
            }
            this.pagination = false
            return responseData
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
        lastPage: null
    })
})
