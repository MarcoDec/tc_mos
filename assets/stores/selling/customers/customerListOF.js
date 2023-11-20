import api from '../../../api'
import {defineStore} from 'pinia'

export const useCustomerListOFStore = defineStore('customerListOF', {
    actions: {
        setIdCustomer(id){
            this.customerID = id
        },
        // async addOFOF(payload){
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
            await api(`/api/manufacturing-orders/${payload}`, 'DELETE')
            this.customerOF = this.customerOF.filter(retard => Number(retard['@id'].match(/\d+/)[0]) !== payload)
        },
        async fetch() {
            if (this.currentPage < 1){
                this.currentPage = 1
            }
            const response = await api(`api/manufacturing-orders?product.customer.id=${this.customerID}&order%5BdeliveryDate%5D=desc`, 'GET')
            this.customerOF = await this.updatePagination(response)
        },
        async filterBy(payload) {
            let url = `api/manufacturing-orders?product.customer.id=${this.customerID}&order%5BdeliveryDate%5D=desc&`
            if (payload === ''){
                await this.fetch()
            } else {
                if (payload.produit !== '') {
                    url += `product.product.name=${payload.produit}&`
                }

                if (payload.dateLivraison !== ''){
                    url += `delivryDate=${payload.dateLivraison}&`
                }

                if (payload.ref !== ''){
                    url += `ref=${payload.ref}&`
                }

                if (payload.indiceClient !== '') {
                    url += `product.product.index=${payload.indiceClient}&`
                }

                if (payload.quantite !== ''){
                    if (payload.quantite.value !== '') {
                        url += `quantityRequested.value=${payload.quantite.value}&`
                    }
                    if (payload.quantite.code !== '') {
                        url += `quantityRequested.code=${payload.quantite.code}&`
                    }
                }

                if (payload.prix !== ''){
                    if (payload.prix.value !== '') {
                        url += `product.product.price.value=${payload.prix.value}&`
                    }
                    if (payload.prix.code !== '') {
                        url += `product.product.price.code=${payload.prix.code}&`
                    }
                }
                url += 'page=1'
                this.currentPage = 1
                const response = await api(url, 'GET')
                this.customerOF = await this.updatePagination(response)
            }
        },
        async itemsPagination(nPage) {
            const response = await api(`api/manufacturing-orders?product.customer.id=${this.customerID}&order%5BdeliveryDate%5D=desc?page=${nPage}`, 'GET')
            this.customerOF = await this.updatePagination(response)
        },
        async paginationSortableOrFilterItems(payload) {
            let response = {}
            if (payload.filter.value === true && payload.sortable.value === true){
                let url = `api/manufacturing-orders?product.customer.id=${this.customerID}&order%5BdeliveryDate%5D=desc&`
                if (payload.filterBy.value.produit !== '') {
                    url += `product.product.name=${payload.filterBy.value.produit}&`
                }

                if (payload.filterBy.value.ref !== ''){
                    url += `ref=${payload.filterBy.value.ref}&`
                }

                if (payload.filterBy.value.indiceClient !== '') {
                    url += `product.product.index=${payload.filterBy.value.indiceClient}&`
                }

                if (payload.filterBy.value.quantite !== ''){
                    if (payload.filterBy.value.quantite.value !== '') {
                        url += `quantityRequested.value=${payload.filterBy.value.quantite.value}&`
                    }
                    if (payload.filterBy.value.quantite.code !== '') {
                        url += `quantityRequested.code=${payload.filterBy.value.quantite.code}&`
                    }
                }

                if (payload.filterBy.value.prix !== ''){
                    if (payload.filterBy.value.prix.value !== '') {
                        url += `product.product.price.value=${payload.filterBy.value.prix.value}&`
                    }
                    if (payload.filterBy.value.prix.code !== '') {
                        url += `product.product.price.code=${payload.filterBy.value.prix.code}&`
                    }
                }

                response = await api(url, 'GET')
                this.customerOF = await this.updatePagination(response)
            } else if (payload.filter.value === true){
                let url = `api/manufacturing-orders?product.customer.id=${this.customerID}&order%5BdeliveryDate%5D=desc&`
                if (payload.filterBy.value.produit !== '') {
                    url += `product.product.name=${payload.filterBy.value.produit}&`
                }

                if (payload.filterBy.value.ref !== ''){
                    url += `ref=${payload.filterBy.value.ref}&`
                }

                if (payload.filterBy.value.indiceClient !== '') {
                    url += `product.product.index=${payload.filterBy.value.indiceClient}&`
                }

                if (payload.filterBy.value.quantite !== ''){
                    if (payload.filterBy.value.quantite.value !== '') {
                        url += `quantityRequested.value=${payload.filterBy.value.quantite.value}&`
                    }
                    if (payload.filterBy.value.quantite.code !== '') {
                        url += `quantityRequested.code=${payload.filterBy.value.quantite.code}&`
                    }
                }

                if (payload.filterBy.value.prix !== ''){
                    if (payload.filterBy.value.prix.value !== '') {
                        url += `product.product.price.value=${payload.filterBy.value.prix.value}&`
                    }
                    if (payload.filterBy.value.prix.code !== '') {
                        url += `product.product.price.code=${payload.filterBy.value.prix.code}&`
                    }
                }

                url += `page=${payload.nPage}`
                response = await api(url, 'GET')
                this.customerOF = await this.updatePagination(response)
            } else if (payload.sortable.value === false) {
                response = await api(`api/manufacturing-orders?product.customer.id=${this.customerID}&order%5BdeliveryDate%5D=desc&page=${payload.nPage}`, 'GET')
                this.customerOF = await this.updatePagination(response)
            } else {
                if (payload.trierAlpha.value.composant === 'component') {
                    response = await api(`api/manufacturing-orders?product.customer.id=${this.customerID}&order%5BdeliveryDate%5D=desc&order%5B${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                } else {
                    response = await api(`api/manufacturing-orders?product.customer.id=${this.customerID}&order%5BdeliveryDate%5D=desc&order%5Baddress.${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                }
                this.customerOF = await this.updatePagination(response)
            }
        },

        async sortableItems(payload, filterBy, filter) {
            let response = {}
            if (filter.value === true){
                if (filterBy.value.produit !== '') {
                    url += `product.product.name=${filterBy.value.produit}&`
                }

                if (filterBy.value.ref !== ''){
                    url += `ref=${filterBy.value.ref}&`
                }

                if (filterBy.value.indiceClient !== '') {
                    url += `product.product.index=${filterBy.value.indiceClient}&`
                }

                if (filterBy.value.quantite !== ''){
                    if (filterBy.value.quantite.value !== '') {
                        url += `quantityRequested.value=${filterBy.value.quantite.value}&`
                    }
                    if (filterBy.value.quantite.code !== '') {
                        url += `quantityRequested.code=${filterBy.value.quantite.code}&`
                    }
                }

                if (filterBy.value.prix !== ''){
                    if (filterBy.value.prix.value !== '') {
                        url += `product.product.price.value=${filterBy.value.prix.value}&`
                    }
                    if (filterBy.value.prix.code !== '') {
                        url += `product.product.price.code=${filterBy.value.prix.code}&`
                    }
                }

                url += `page=${this.currentPage}`
                response = await api(url, 'GET')
                this.customerOF = await this.updatePagination(response)
            } else {
                if (payload.composant === 'component') {
                    response = await api(`api/manufacturing-orders?product.customer.id=${this.customerID}&order%5BdeliveryDate%5D=desc&order%5B${payload.composant}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                } else {
                    response = await api(`api/manufacturing-orders?product.customer.id=${this.customerID}&order%5BdeliveryDate%5D=desc&order%5B${payload.produit}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                }
                this.customerOF = await this.updatePagination(response)
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
        itemsCustomerOF: state => state.customerOF.map(item => {
            const dtLivraison = item.deliveryDate.split('T')[0]
            const newObject = {
                '@id': item['@id'],
                produit: item.product.product.name,
                ref: item.ref,
                indiceClient: item.product.product.index,
                dateLivraison: dtLivraison,
                quantite: `${item.quantityRequested.value} ${item.quantityRequested.code}`,
                prix: `${item.product.product.price.value} ${item.product.product.price.code}`
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
        customerOF: [],
        customerID: 0
    })
})
