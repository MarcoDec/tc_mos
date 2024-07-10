import {defineStore} from 'pinia'
import api from '../../api'

export const useFacturesCustomerOrderItemsStore = defineStore('facturesCustomerOrderItems', {
    actions: {
        async fetch(criteria = '') {
            const response = await api(`/api/bills${criteria}`, 'GET')
            this.factureCustomerOrderItems = await this.updatePagination(response)
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
            await api(`/api/bills/${id}`, 'DELETE')
            this.factureCustomerOrderItems = this.factureCustomerOrderItems.filter(factureCustomerOrderItem => Number(factureCustomerOrderItem['@id'].match(/\d+/)[0]) !== id)
        }
    },
    getters: {

        itemsFactureCustomerOrder: state => state.factureCustomerOrderItems.map(item => {
            const newObject = {
                '@id': item['@id'],
                invoiceNumber: item.ref,
                totalHT: {
                    code: item.exclTax.code,
                    value: item.exclTax.value
                },
                totalTTC: {
                    code: item.inclTax.code,
                    value: item.inclTax.value
                },
                vta: {
                    code: item.vat.code,
                    value: item.vat.value
                },
                invoiceDate: item.billingDate,
                deadlineDate: item.dueDate,
                currentPlace: item.embState.state
            }
            return newObject
        })
    },

    state: () => ({
        factureCustomerOrderItems: []
    })
})
