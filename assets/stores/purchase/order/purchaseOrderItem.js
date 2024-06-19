import {defineStore} from 'pinia'
import api from '../../../api'

export const usePurchaseOrderItemComponentsStore = defineStore('purchaseOrderItem', {
    actions: {
        async fetch(criteria = '') {
            const response = await api(`/api/purchase-order-item-components${criteria}`, 'GET')
            this.purchaseOrderitemComponents = await this.updatePagination(response)
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
            await api(`/api/purchase-order-items/${id}`, 'DELETE')
            this.purchaseOrderitemComponents = this.purchaseOrderitemComponents.filter(purchaseOrderitemComponent => Number(purchaseOrderitemComponent['@id'].match(/\d+/)[0]) !== id)
        },
        async fetchById(id) {
            const response = await api(`/api/purchase-order-item-components/${id}`, 'GET')
            this.purchaseOrderitemComponent = response
        }
    },
    getters: {
        itemsPurchaseOrderItemComponents: state => state.purchaseOrderitemComponents.map(item => {
            const newObject = {
                '@id': item['@id'],
                component: item.item,
                product: null,
                ref: item.item.manufacturerCode,
                requestedQuantity: {
                    code: item.requestedQuantity.code,
                    value: item.requestedQuantity.value
                },
                requestedDate: item.requestedDate,
                confirmedQuantity: {
                    code: item.confirmedQuantity.code,
                    value: item.confirmedQuantity.value
                },
                confirmedDate: item.confirmedDate,
                etat: item.embState.state ? item.embState.state : null,
                notes: item.notes,
                targetCompany: item.targetCompany
            }
            return newObject
        })
    },

    state: () => ({
        purchaseOrderitemComponents: [],
        purchaseOrderitemComponent: [],
        pagination: false,
        firstPage: "",
        lastPage: "",
        nextPage: "",
        currentPage: "",
        previousPage: ""
    })
})
