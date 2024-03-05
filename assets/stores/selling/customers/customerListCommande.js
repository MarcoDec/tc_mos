import api from '../../../api'
import {defineStore} from 'pinia'

export const useCustomerListCommandeStore = defineStore('customerListCommande', {
    actions: {
        setIdCustomer(id){
            this.customerID = id
        },
        // async addCommandeCommande(payload){
        //     const violations = []
        //     try {
        //         if (payload.quantite.value !== ''){
        //             payload.quantite.value = parseInt(payload.quantite.value)
        //         }
        //         const element = {
        //             component: payload.composant,
        //             refFournisseur: payload.refFournisseur,
        //             prix: payload.prix,
        //             quantity: payload.quantite,
        //             texte: payload.texte
        //         }
        //         await api('/api/component-stocks', 'POST', element)
        //         this.fetch()
        //     } catch (error) {
        //         violations.push({message: error})
        //     }
        //     return violations
        // },
        async deleted(payload) {
            await api(`/api/customer-products/${payload}`, 'DELETE')
            this.customerCommande = this.customerCommande.filter(retard => Number(retard['@id'].match(/\d+/)[0]) !== payload)
        },
        async fetch() {
            // const response = await api(`/api/selling-order-items?customer=${this.customerID}`, 'GET')
            if (this.currentPage < 1){
                this.currentPage = 1
            }
            // const response = await api(`/api/selling-order-items/customerFilter/${this.customerID}?page=${this.currentPage}`, 'GET')
            const response = await api(`/api/selling-order-products?order.customer.id=/api/customers/${this.customerID}`, 'GET')
            this.customerCommande = await this.updatePagination(response)
        },
        async filterBy(payload) {
            let url = `/api/selling-order-products?order.customer.id=/api/customers/${this.customerID}&`
            if (payload === ''){
                await this.fetch()
            } else {
                if (payload.ref !== '') {
                    url += `order.ref=${payload.ref}&`
                }

                if (payload.etat !== '') {
                    url += `embState.state=${payload.etat}&`
                }

                if (payload.dateConfirmee !== '') {
                    url += `confirmedDate=${payload.dateConfirmee}&`
                }
                if (payload.typeCommande !== '') {
                    url += `order.kind=${payload.typeCommande}&`
                }
                if (payload.dateSouhaitee !== '') {
                    url += `requestedDate=${payload.dateSouhaitee}&`
                }

                if (payload.quantiteSouhaitee !== ''){
                    if (payload.quantiteSouhaitee.value !== '') {
                        url += `requestedQuantity.value=${payload.quantiteSouhaitee.value}&`
                    }
                    if (payload.quantiteSouhaitee.code !== '') {
                        url += `requestedQuantity.code=${payload.quantiteSouhaitee.code}&`
                    }
                }

                if (payload.quantiteEffectuee !== ''){
                    if (payload.quantiteEffectuee.value !== '') {
                        url += `confirmedQuantity.value=${payload.quantiteEffectuee.value}&`
                    }
                    if (payload.quantiteEffectuee.code !== '') {
                        url += `confirmedQuantity.code=${payload.quantiteEffectuee.code}&`
                    }
                }
                url += 'page=1'
                this.currentPage = 1
                const response = await api(url, 'GET')
                this.customerCommande = await this.updatePagination(response)
            }
        },
        async itemsPagination(nPage) {
            const response = await api(`/api/selling-order-product?order.customer=/api/customers/${this.customerID}?page=${nPage}`, 'GET')
            this.customerCommande = await this.updatePagination(response)
        },
        async paginationSortableOrFilterItems(payload) {
            let response = {}
            if (payload.filter.value === true && payload.sortable.value === true){
                let url = `/api/selling-order-products?order.customer.id=/api/customers/${this.customerID}&`
                if (payload.filterBy.value.ref !== '') {
                    url += `order.ref=${payload.filterBy.value.ref}&`
                }

                if (payload.filterBy.value.etat !== '') {
                    url += `embState.state=${payload.filterBy.value.etat}&`
                }

                if (payload.filterBy.value.dateConfirmee !== '') {
                    url += `confirmedDate=${payload.filterBy.value.dateConfirmee}&`
                }
                if (payload.filterBy.value.typeCommande !== '') {
                    url += `order.kind=${payload.filterBy.value.typeCommande}&`
                }
                if (payload.filterBy.value.dateSouhaitee !== '') {
                    url += `requestedDate=${payload.filterBy.value.dateSouhaitee}&`
                }

                if (payload.filterBy.value.quantiteSouhaitee !== ''){
                    if (payload.filterBy.value.quantiteSouhaitee.value !== '') {
                        url += `requestedQuantity.value=${payload.filterBy.value.quantiteSouhaitee.value}&`
                    }
                    if (payload.filterBy.value.quantiteSouhaitee.code !== '') {
                        url += `requestedQuantity.code=${payload.filterBy.value.quantiteSouhaitee.code}&`
                    }
                }

                if (payload.filterBy.value.quantiteEffectuee !== ''){
                    if (payload.filterBy.value.quantiteEffectuee.value !== '') {
                        url += `confirmedQuantity.value=${payload.filterBy.value.quantiteEffectuee.value}&`
                    }
                    if (payload.filterBy.value.quantiteEffectuee.code !== '') {
                        url += `confirmedQuantity.code=${payload.filterBy.value.quantiteEffectuee.code}&`
                    }
                }

                response = await api(url, 'GET')
                this.customerCommande = await this.updatePagination(response)
            } else if (payload.filter.value === true){
                let url = `/api/selling-order-products?order.customer.id=/api/customers/${this.customerID}&`
                if (payload.filterBy.value.ref !== '') {
                    url += `order.ref=${payload.filterBy.value.ref}&`
                }

                if (payload.filterBy.value.etat !== '') {
                    url += `embState.state=${payload.filterBy.value.etat}&`
                }

                if (payload.filterBy.value.dateConfirmee !== '') {
                    url += `confirmedDate=${payload.filterBy.value.dateConfirmee}&`
                }
                if (payload.filterBy.value.typeCommande !== '') {
                    url += `order.kind=${payload.filterBy.value.typeCommande}&`
                }
                if (payload.filterBy.value.dateSouhaitee !== '') {
                    url += `requestedDate=${payload.filterBy.value.dateSouhaitee}&`
                }

                if (payload.filterBy.value.quantiteSouhaitee !== ''){
                    if (payload.filterBy.value.quantiteSouhaitee.value !== '') {
                        url += `requestedQuantity.value=${payload.filterBy.value.quantiteSouhaitee.value}&`
                    }
                    if (payload.filterBy.value.quantiteSouhaitee.code !== '') {
                        url += `requestedQuantity.code=${payload.filterBy.value.quantiteSouhaitee.code}&`
                    }
                }

                if (payload.filterBy.value.quantiteEffectuee !== ''){
                    if (payload.filterBy.value.quantiteEffectuee.value !== '') {
                        url += `confirmedQuantity.value=${payload.filterBy.value.quantiteEffectuee.value}&`
                    }
                    if (payload.filterBy.value.quantiteEffectuee.code !== '') {
                        url += `confirmedQuantity.code=${payload.filterBy.value.quantiteEffectuee.code}&`
                    }
                }
                url += `page=${payload.nPage}`
                response = await api(url, 'GET')
                this.customerCommande = await this.updatePagination(response)
            } else if (payload.sortable.value === false) {
                response = await api(`/api/selling-order-products?order.customer.id=/api/customers/${this.customerID}&page=${payload.nPage}`, 'GET')
                this.customerCommande = await this.updatePagination(response)
            } else {
                if (payload.trierAlpha.value.composant === 'component') {
                    response = await api(`/api/selling-order-products?order.customer.id=/api/customers/${this.customerID}&order%5B${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                } else {
                    response = await api(`/api/selling-order-products?order.customer.id=/api/customers/${this.customerID}&order%5Baddress.${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                }
                this.customerCommande = await this.updatePagination(response)
            }
        },

        async sortableItems(payload, filterBy, filter) {
            let response = {}
            if (filter.value === true){
                if (filterBy.value.ref !== '') {
                    url += `order.ref=${filterBy.value.ref}&`
                }

                if (filterBy.value.etat !== '') {
                    url += `embState.state=${filterBy.value.etat}&`
                }

                if (filterBy.value.dateConfirmee !== '') {
                    url += `confirmedDate=${filterBy.value.dateConfirmee}&`
                }
                if (filterBy.value.typeCommande !== '') {
                    url += `order.kind=${filterBy.value.typeCommande}&`
                }
                if (filterBy.value.dateSouhaitee !== '') {
                    url += `requestedDate=${filterBy.value.dateSouhaitee}&`
                }

                if (filterBy.value.quantiteSouhaitee !== ''){
                    if (filterBy.value.quantiteSouhaitee.value !== '') {
                        url += `requestedQuantity.value=${filterBy.value.quantiteSouhaitee.value}&`
                    }
                    if (filterBy.value.quantiteSouhaitee.code !== '') {
                        url += `requestedQuantity.code=${filterBy.value.quantiteSouhaitee.code}&`
                    }
                }

                if (filterBy.value.quantiteEffectuee !== ''){
                    if (filterBy.value.quantiteEffectuee.value !== '') {
                        url += `confirmedQuantity.value=${filterBy.value.quantiteEffectuee.value}&`
                    }
                    if (filterBy.value.quantiteEffectuee.code !== '') {
                        url += `confirmedQuantity.code=${filterBy.value.quantiteEffectuee.code}&`
                    }
                }

                url += `page=${this.currentPage}`
                response = await api(url, 'GET')
                this.customerCommande = await this.updatePagination(response)
            } else {
                if (payload.composant === 'component') {
                    response = await api(`/api/selling-order-products?order.customer.id=/api/customers/${this.customerID}&order%5B${payload.composant}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                } else {
                    response = await api(`/api/selling-order-products?order.customer.id=/api/customers/${this.customerID}&order%5B${payload.produit}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                }
                this.customerCommande = await this.updatePagination(response)
            }
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
        // async updateWarehouseStock(payload){
        //     await api(`/api/stocks/${payload.id}`, 'PATCH', payload.itemsUpdateData)
        //     if (payload.sortable.value === true || payload.filter.value === true) {
        //         this.paginationSortableOrFilterItems({filter: payload.filter, filterBy: payload.filterBy, nPage: this.currentPage, sortable: payload.sortable, trierAlpha: payload.trierAlpha})
        //     } else {
        //         this.itemsPagination(this.currentPage)
        //     }
        //     this.fetch()
        // }
    },
    getters: {
        itemsCustomerCommande: state => state.customerCommande.map(item => {
            const newObject = {
                '@id': item['@id'],
                ref: item.order.ref,
                dateSouhaitee: item.requestedDate,
                dateConfirmee: item.confirmedDate,
                etat: item.embState.state,
                typeCommande: item.order.kind,
                destination: item.order.destination,
                quantiteEffectuee: `${item.confirmedQuantity.value} ${item.confirmedQuantity.code}`,
                quantiteSouhaitee: `${item.requestedQuantity.value} ${item.requestedQuantity.code}`
            }
            return newObject
        })
    },
    state: () => ({
        currentPage: '',
        firstPage: '',
        lastPage: '',
        nextPage: '',
        pagination: false,
        previousPage: '',
        customerCommande: [],
        customerID: 0
    })
})
