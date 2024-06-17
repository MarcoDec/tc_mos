import {defineStore} from 'pinia'
import api from '../../api'

export const useOfCustomerOrderItemsStore = defineStore('ofCustomerOrderItems', {
    actions: {
        async fetch(criteria = '') {
            const response = await api(`/api/manufacturing-orders${criteria}`, 'GET')
            this.ofCustomerOrderItems = this.updatePagination(response)
            this.ofCustomerOrderItems.forEach(ofCustomerOrderItem => {
                ofCustomerOrderItem.product = ofCustomerOrderItem.product['@id']
            })
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
        },
        async remove(id){
            await api(`/api/manufacturing-orders/${id}`, 'DELETE')
            this.ofCustomerOrderItems = this.ofCustomerOrderItems.filter(ofCustomerOrderItem => Number(ofCustomerOrderItem['@id'].match(/\d+/)[0]) !== id)
        }
    },
    getters: {
        itemsofCustomerOrder: state => state.ofCustomerOrderItems.map(item => {
            const newObject = {
                '@id': item['@id'],
                manufacturingCompany: item.manufacturingCompany,
                quantityRequested: {
                    code: item.quantityRequested.code,
                    value: item.quantityRequested.value
                },
                currentPlace: item.embState.state,
                product: item.product,
                manufacturingDate: item.manufacturingDate,
                deliveryDate: item.deliveryDate,
            }
            return newObject
        })
    },

    state: () => ({
        ofCustomerOrderItems: []
    })
})
