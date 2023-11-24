import {defineStore} from 'pinia'
import api from '../../api'


export const useCustomerOrderItemsStore = defineStore('customerOrderItems', {
    actions: {
        async fetch(criteria = '') {
            const response = await api(`/api/selling-order-items${criteria}`, 'GET')
            this.customerOrders = await this.updatePagination(response)
            console.log('customerOrders', this.customerOrders);
        },
        async updatePagination(response) {
            const responseData = await response['hydra:member']
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
        },
        async remove(id){
            await api(`/api/selling-order-items/${id}`, 'DELETE')
            this.customerOrders = this.customerOrders.filter(customerOrder => Number(customerOrder['@id'].match(/\d+/)[0]) !== id)
        }

    },
    getters: {
        itemsCustomerOrders: state => state.customerOrders.map(item => {
            const newObject = {
                '@id': item['@id'], 
                confirmedDate:item.confirmedDate,
                confirmedQuantity: item.confirmedQuantity.value + " " + item.confirmedQuantity.code,
                requestedDate:item.requestedDate,
                requestedQuantity: item.requestedQuantity.value + " " + item.requestedQuantity.code,
                ref:item.ref,
                state:item.embState.state,
                notes:item.notes,
                product:item.item,
                id:item.id
            }
            return newObject
        }),
        ariaSort() {
            return field => (this.isSorter(field) ? this.order : 'none')
        },
        fetchBody() {
            return {page: this.current, ...this.search, ...this.orderBody}
        },
        isSorter: state => field => field.name === state.sorted,
        order: state => (state.asc ? 'ascending' : 'descending'),
        pages: state => Math.ceil(state.total / 15)

    },
    state: () => ({
        asc: true,
        current: 1,
        first: 1,
        items: [],
        last: 1,
        next: 1,
        prev: 1,
        search: {},
        total: 0,
        customerOrders: {}
    })
})
