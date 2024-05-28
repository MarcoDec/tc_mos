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
            await this.fetchById(payload.id)
        },
        async remove(id) {
            await api(`/api/selling-orders/${id}`, 'DELETE')
            await this.fetch()
        },
        async hasActiveEdiOrders(iriCustomer, idOrder) {
            const response = await api(`/api/selling-orders?customer=${iriCustomer}&deleted=false&pagination=false`, 'GET')
            const results = response['hydra:member']
            //Afin de permettre le changement de type edi de la commande en cours, on retire du tableau de résultat la commande courante
            const resultsFiltered = results.filter(
                anOrder => anOrder.id !== idOrder
            )
            return resultsFiltered.some(order => ['edi_orders', 'edi_delfor'].includes(order.orderFamily))
        },
        async hasActiveForecastOrders(iriCustomer, idOrder) {
            const response = await api(`/api/selling-orders?customer=${iriCustomer}&deleted=false&pagination=false`, 'GET')
            const results = response['hydra:member']
            //Afin de permettre le changement de type edi de la commande en cours, on retire du tableau de résultat la commande courante
            const resultsFiltered = results.filter(
                anOrder => anOrder.id !== idOrder
            )
            return resultsFiltered.some(order => ['forecast', 'edi_delfor'].includes(order.orderFamily))
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
        customerWithIntegratedEdi() {
            // console.log(this.selectedCustomer)
            if (this.selectedCustomer !== null && this.selectedCustomer.isEdiOrders && this.selectedCustomer.ediKind === 'integratedEDI') {
                return true
            }
            return false
        },
        orderFamilyOptions() {
            // const baseOptions = [{text: 'Libre', value: 'free'}]
            const baseOptions = []
            if (!this.customerWithIntegratedEdi()) {
                baseOptions.push({text: 'Ferme', value: 'fixed'})
                baseOptions.push({text: 'Prévisionnelle', value: 'forecast'})
                return baseOptions
            }
            if (this.selectedCustomer.ediOrderType === 'ORDERS') {
                baseOptions.push({text: 'Prévisionnelle', value: 'forecast'})
                baseOptions.push({text: 'Ferme (EDI ORDERS)', value: 'edi_orders'})
            } else {
                baseOptions.push({text: 'Prévisionnelle (EDI DELFOR)', value: 'edi_delfor'})
            }
            return baseOptions
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
        selectedCustomer: {}
    })
})
