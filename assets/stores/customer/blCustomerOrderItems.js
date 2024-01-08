import {defineStore} from 'pinia'
import api from '../../api'

export const useBlCustomerOrderItemsStore = defineStore('blCustomerOrderItems', {
    actions: {
        async fetch(criteria = '') {
            const response = await api(`/api/delivery-notes${criteria}`, 'GET')
            this.blCustomerOrderItems = await this.updatePagination(response)
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
            await api(`/api/delivery-notes/${id}`, 'DELETE')
            this.blCustomerOrderItems = this.blCustomerOrderItems.filter(blCustomerOrderItem => Number(blCustomerOrderItem['@id'].match(/\d+/)[0]) !== id)
        }
    },
    getters: {

        itemsBlCustomerOrder: state => state.blCustomerOrderItems.map(item => {
            const newObject = {
                '@id': item['@id'],
                currentPlace: item.embState.state,
                departureDate: item.date,
                number: item.ref
            }
            return newObject
        })
    },

    state: () => ({
        blCustomerOrderItems: []
    })
})
