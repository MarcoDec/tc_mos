import api from '../../../api'
import {defineStore} from 'pinia'

export const usePurchaseOrderStore = defineStore('purchaseOrder', {
    actions: {
        async addPurchaseOrder(payload) {
            await api('/api/purchase-orders', 'POST', payload)
        },
        async fetch(criteria) {
            const response = await api(`/api/purchase-orders${criteria}`, 'GET')
            this.purchaseOrders = response['hydra:member']
            this.purchaseOrders.forEach(order => {
                order.state = order.embState.state
                order.closer = order.embBlocker.state
            })
            this.updatePagination(response)
        },
        async fetchById(id) {
            this.purchaseOrder = await api(`/api/purchase-orders/${id}`, 'GET')
            this.purchaseOrder.state = this.purchaseOrder.embState.state
            this.purchaseOrder.closer = this.purchaseOrder.embBlocker.state
        },
        async updatePurchaseOrder(payload) {
            // console.log(payload)
            await api(`/api/purchase-orders/${payload.id}`, 'PATCH', payload.PurchaseOrder)
            await this.fetchById(payload.id)
        },
        async remove(id) {
            await api(`/api/purchase-orders/${id}`, 'DELETE')
            this.purchaseOrders = this.purchaseOrders.filter(purchaseOrder => purchaseOrder.id !== id)
        },
        async hasActiveEdiOrders(iriSupplier, idOrder) {
            const response = await api(`/api/purchase-orders?supplier=${iriSupplier}&deleted=false&pagination=false`, 'GET')
            const results = response['hydra:member']
            //Afin de permettre le changement de type edi de la commande en cours, on retire du tableau de résultat la commande courante
            const resultsFiltered = results.filter(
                anOrder => anOrder.id !== idOrder
            )
            return resultsFiltered.some(order => ['edi_orders', 'edi_delfor'].includes(order.orderFamily))
        },
        async hasActiveForecastOrders(iriSupplier, idOrder) {
            const response = await api(`/api/purchase-orders?supplier=${iriSupplier}&deleted=false&pagination=false`, 'GET')
            const results = response['hydra:member']
            //Afin de permettre le changement de type edi de la commande en cours, on retire du tableau de résultat la commande courante
            const resultsFiltered = results.filter(
                anOrder => anOrder.id !== idOrder
            )
            return resultsFiltered.some(order => ['forecast', 'edi_delfor'].includes(order.orderFamily))
        },
        supplierWithIntegratedEdi() {
            // console.log(this.selectedCustomer)
            if (this.selectedSupplier !== null && this.selectedSupplier.isEdiOrders && this.selectedSupplier.ediKind === 'integratedEDI') {
                return true
            }
            return false
        },
        orderFamilyOptions() {
            // const baseOptions = [{text: 'Libre', value: 'free'}]
            const baseOptions = []
            if (!this.supplierWithIntegratedEdi()) {
                baseOptions.push({text: 'Ferme', value: 'fixed'})
                baseOptions.push({text: 'Prévisionnelle', value: 'forecast'})
                return baseOptions
            }
            if (this.selectedSupplier.ediOrderType === 'ORDERS') {
                baseOptions.push({text: 'Prévisionnelle', value: 'forecast'})
                baseOptions.push({text: 'Ferme (EDI ORDERS)', value: 'edi_orders'})
            } else {
                baseOptions.push({text: 'Prévisionnelle (EDI DELFOR)', value: 'edi_delfor'})
            }
            return baseOptions
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
                this.lastPage = paginationView['hydra:last'] ? paginationView['hydra:last'].match(/page=(\d+)/)[1] : '1'
                this.nextPage = paginationView['hydra:next'] ? paginationView['hydra:next'].match(/page=(\d+)/)[1] : '1'
                this.previousPage = paginationView['hydra:previous'] ? paginationView['hydra:previous'].match(/page=(\d+)/)[1] : '1'
                this.currentPage = paginationView['@id'].match(/page=(\d+)/)[1]
                return responseData
            }
            this.pagination = false
            return responseData
        }
    },
    getters: {
        order: state => state.purchaseOrder.order,
        supplier: state => state.purchaseOrder.supplier
    },
    state: () => ({
        purchaseOrders: [],
        purchaseOrder: {},
        currentPage: 1,
        firstPage: 1,
        nextPage: null,
        pagination: true,
        previousPage: null,
        lastPage: null,
        selectedSupplier: {}
    })
})
