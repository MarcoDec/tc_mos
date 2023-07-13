import api from '../../../../api'
import {defineStore} from 'pinia'

export const useCustomerListProductStore = defineStore('customerListProduct', {
    actions: {
        setIdCustomer(id){
            this.customerID = id
        },
        // async addProductProduct(payload){
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
            this.customerProduct = this.customerProduct.filter(retard => Number(retard['@id'].match(/\d+/)[0]) !== payload)
        },
        async fetch() {
            // const response = await api(`/api/selling-order-items?customer=${this.customerID}`, 'GET')
            if (this.currentPage < 1){
                this.currentPage = 1
            }
            // const response = await api(`/api/selling-order-items/customerFilter/${this.customerID}?page=${this.currentPage}`, 'GET')
            const response = await api(`/api/customer-products?customer=/api/customers/${this.customerID}`, 'GET')
            this.customerProduct = await this.updatePagination(response)
        },
        async filterBy(payload) {
            let url = `/api/customer-products?customer=/api/customers/${this.customerID}&`
            if (payload === ''){
                await this.fetch()
            } else {
                if (payload.produit !== '') {
                    url += `product.name=${payload.produit}&`
                }

                if (payload.ref !== ''){
                    url += `product.of=${payload.ref}&`
                }

                if (payload.indiceClient !== '') {
                    url += `product.index=${payload.indiceClient}&`
                }

                if (payload.quantite !== ''){
                    if (payload.quantite.value !== '') {
                        url += `product.forecastVolume.value=${payload.quantite.value}&`
                    }
                    if (payload.quantite.code !== '') {
                        url += `product.forecastVolume.code=${payload.quantite.code}&`
                    }
                }

                if (payload.prix !== ''){
                    if (payload.prix.value !== '') {
                        url += `product.price.value=${payload.prix.value}&`
                    }
                    if (payload.prix.code !== '') {
                        url += `product.price.code=${payload.prix.code}&`
                    }
                }
                url += 'page=1'
                this.currentPage = 1
                const response = await api(url, 'GET')
                this.customerProduct = await this.updatePagination(response)
            }
        },
        async itemsPagination(nPage) {
            const response = await api(`/api/customer-products?customer=/api/customers/${this.customerID}?page=${nPage}`, 'GET')
            this.customerProduct = await this.updatePagination(response)
        },
        async paginationSortableOrFilterItems(payload) {
            let response = {}
            if (payload.filter.value === true && payload.sortable.value === true){
                let url = `/api/customer-products?customer=/api/customers/${this.customerID}&`
                if (payload.filterBy.value.produit !== '') {
                    url += `product.name=${payload.filterBy.value.produit}&`
                }

                if (payload.filterBy.value.ref !== ''){
                    url += `product.of=${payload.filterBy.value.ref}&`
                }

                if (payload.filterBy.value.indiceClient !== '') {
                    url += `product.index=${payload.filterBy.value.indiceClient}&`
                }

                if (payload.filterBy.value.quantite !== ''){
                    if (payload.filterBy.value.quantite.value !== '') {
                        url += `product.forecastVolume.value=${payload.filterBy.value.quantite.value}&`
                    }
                    if (payload.filterBy.value.quantite.code !== '') {
                        url += `product.forecastVolume.code=${payload.filterBy.value.quantite.code}&`
                    }
                }

                if (payload.filterBy.value.prix !== ''){
                    if (payload.filterBy.value.prix.value !== '') {
                        url += `product.price.value=${payload.filterBy.value.prix.value}&`
                    }
                    if (payload.filterBy.value.prix.code !== '') {
                        url += `product.price.code=${payload.filterBy.value.prix.code}&`
                    }
                }

                response = await api(url, 'GET')
                this.customerProduct = await this.updatePagination(response)
            } else if (payload.filter.value === true){
                let url = `/api/customer-products?customer=/api/customers/${this.customerID}&`
                if (payload.filterBy.value.produit !== '') {
                    url += `product.name=${payload.filterBy.value.produit}&`
                }

                if (payload.filterBy.value.ref !== ''){
                    url += `product.of=${payload.filterBy.value.ref}&`
                }

                if (payload.filterBy.value.indiceClient !== '') {
                    url += `product.index=${payload.filterBy.value.indiceClient}&`
                }

                if (payload.filterBy.value.quantite !== ''){
                    if (payload.filterBy.value.quantite.value !== '') {
                        url += `product.forecastVolume.value=${payload.filterBy.value.quantite.value}&`
                    }
                    if (payload.filterBy.value.quantite.code !== '') {
                        url += `product.forecastVolume.code=${payload.filterBy.value.quantite.code}&`
                    }
                }

                if (payload.filterBy.value.prix !== ''){
                    if (payload.filterBy.value.prix.value !== '') {
                        url += `product.price.value=${payload.filterBy.value.prix.value}&`
                    }
                    if (payload.filterBy.value.prix.code !== '') {
                        url += `product.price.code=${payload.filterBy.value.prix.code}&`
                    }
                }

                url += `page=${payload.nPage}`
                response = await api(url, 'GET')
                this.customerProduct = await this.updatePagination(response)
            } else if (payload.sortable.value === false) {
                response = await api(`/api/customer-products?customer=/api/customers/${this.customerID}&page=${payload.nPage}`, 'GET')
                this.customerProduct = await this.updatePagination(response)
            } else {
                if (payload.trierAlpha.value.composant === 'component') {
                    response = await api(`/api/customer-products?customer=/api/customers/${this.customerID}&order%5B${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                } else {
                    response = await api(`/api/customer-products?customer=/api/customers/${this.customerID}&order%5Baddress.${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                }
                this.customerProduct = await this.updatePagination(response)
            }
        },

        async sortableItems(payload, filterBy, filter) {
            let response = {}
            if (filter.value === true){
                if (filterBy.value.produit !== '') {
                    url += `product.name=${filterBy.value.produit}&`
                }

                if (filterBy.value.ref !== ''){
                    url += `product.of=${filterBy.value.ref}&`
                }

                if (filterBy.value.indiceClient !== '') {
                    url += `product.index=${filterBy.value.indiceClient}&`
                }

                if (filterBy.value.quantite !== ''){
                    if (filterBy.value.quantite.value !== '') {
                        url += `product.forecastVolume.value=${filterBy.value.quantite.value}&`
                    }
                    if (filterBy.value.quantite.code !== '') {
                        url += `product.forecastVolume.code=${filterBy.value.quantite.code}&`
                    }
                }

                if (filterBy.value.prix !== ''){
                    if (filterBy.value.prix.value !== '') {
                        url += `product.price.value=${filterBy.value.prix.value}&`
                    }
                    if (filterBy.value.prix.code !== '') {
                        url += `product.price.code=${filterBy.value.prix.code}&`
                    }
                }

                url += `page=${this.currentPage}`
                response = await api(url, 'GET')
                this.customerProduct = await this.updatePagination(response)
            } else {
                if (payload.composant === 'component') {
                    response = await api(`/api/customer-products?customer=/api/customers/${this.customerID}&order%5B${payload.composant}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                } else {
                    response = await api(`/api/customer-products?customer=/api/customers/${this.customerID}&order%5B${payload.produit}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                }
                this.customerProduct = await this.updatePagination(response)
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
        itemsCustomerProduct: state => state.customerProduct.map(item => {
            const newObject = {
                '@id': item['@id'],
                produit: item.product.name,
                ref: item.product.code,
                indiceClient: item.product.index,
                quantite: item.product.forecastVolume,
                prix: item.product.price
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
        customerProduct: [],
        customerID: 0
    })
})
