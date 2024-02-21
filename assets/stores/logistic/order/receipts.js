import {defineStore} from 'pinia'
import api from '../../../api'

export const useReceiptsStore = defineStore('receiptsItem', {
    actions: {
        async fetch(criteria = '') {
            const response = await api(`api/receipts${criteria}`, 'GET')
            this.receiptsItems = await this.updatePagination(response)
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
        }
    },
    getters: {
        itemsReceipts: state => state.receiptsItems.map(item => {
            const newObject = {
                '@id': item['@id'],
                component: item.item,
                // confirmedQuantity: {
                //     code: item.requestedQuantity.code,
                //     value: item.requestedQuantity.value
                // },
                quantityReceived: {
                    code: item.quantity.code,
                    value: item.quantity.value
                },
                requestedDate: item.date,
                etat: item.embState.state ? item.embState.state : null
            }
            return newObject
        })
    },

    state: () => ({
        receiptsItems: [],
        pagination : false,
        firstPage: "",
        lastPage : "",
        nextPage : "",
        currentPage : "",
        previousPage : ""
    })
})