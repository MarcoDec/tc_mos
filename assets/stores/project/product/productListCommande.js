import api from '../../../api'
import {defineStore} from 'pinia'

export const useProductListCommandeStore = defineStore('productListCommande', {
    actions: {
        setIdProduct(id){
            this.productID = id
        },
        // async addProductCommande(payload){
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
            await api(`/api/selling-order-items/${payload}`, 'DELETE')
            this.productCommande = this.productCommande.filter(retard => Number(retard['@id'].match(/\d+/)[0]) !== payload)
        },
        async fetch() {
            // const response = await api(`/api/selling-order-items?product=${this.productID}`, 'GET')
            if (this.currentPage < 1){
                this.currentPage = 1
            }
            // const response = await api(`/api/selling-order-items/productFilter/${this.productID}?page=${this.currentPage}`, 'GET')
            const response = await api(`/api/selling-order-products?item=/api/products/${this.productID}`, 'GET')
            this.productCommande = await this.updatePagination(response)
        },
        async filterBy(payload) {
            let url = `/api/selling-order-products?item=/api/products/${this.productID}&`
            if (payload === ''){
                await this.fetch()
            } else {
                if (payload.client !== '') {
                    url += `order.customer.name=${payload.client}&`
                }

                if (payload.reference !== ''){
                    url += `ref=${payload.reference}&`
                }

                if (payload.quantiteSouhaitee !== ''){
                    if (payload.quantiteSouhaitee.value !== '') {
                        url += `requestedQuantity.value=${payload.quantiteSouhaitee.value}&`
                    }
                    if (payload.quantiteSouhaitee.code !== '') {
                        url += `requestedQuantity.code=${payload.quantiteSouhaitee.code}&`
                    }
                }
                if (payload.quantiteConfirmee !== ''){
                    if (payload.quantiteConfirmee.value !== '') {
                        url += `confirmedQuantity.value=${payload.quantiteConfirmee.value}&`
                    }
                    if (payload.quantiteConfirmee.code !== '') {
                        url += `confirmedQuantity.code=${payload.quantiteConfirmee.code}&`
                    }
                }

                if (payload.dateLivraison !== ''){
                    url += `confirmedDate=${payload.dateLivraison}&`
                }

                if (payload.dateLivraisonSouhaitee !== ''){
                    url += `requestedDate=${payload.dateLivraisonSouhaitee}&`
                }
                url += 'page=1'
                this.currentPage = 1
                const response = await api(url, 'GET')
                this.productCommande = await this.updatePagination(response)
            }
        },
        async itemsPagination(nPage) {
            const response = await api(`/api/selling-order-items/productFilter/${this.productID}?page=${nPage}`, 'GET')
            this.productCommande = await this.updatePagination(response)
        },
        async paginationSortableOrFilterItems(payload) {
            let response = {}
            if (payload.filter.value === true && payload.sortable.value === true){
                if (payload.trierAlpha.value.component === 'component') {
                    let url = `/api/selling-order-products?item=/api/products/${this.productID}&`
                    if (payload.filterBy.value.client !== ''){
                        url += `order.customer.name=${payload.filterBy.value.client}&`
                    }
                    if (payload.filterBy.value.reference !== ''){
                        url += `ref=${payload.filterBy.value.reference}&`
                    }

                    if (payload.filterBy.value.quantiteSouhaitee !== ''){
                        if (payload.filterBy.value.quantiteSouhaitee.value !== '') {
                            url += `requestedQuantity.value=${payload.filterBy.value.quantiteSouhaitee.value}&`
                        }
                        if (payload.filterBy.value.quantiteSouhaitee.code !== '') {
                            url += `requestedQuantity.code=${payload.filterBy.value.quantiteSouhaitee.code}&`
                        }
                    }

                    if (payload.filterBy.value.quantiteConfirmee !== ''){
                        if (payload.filterBy.value.quantiteConfirmee.value !== '') {
                            url += `confirmedQuantity.value=${payload.filterBy.value.quantiteConfirmee.value}&`
                        }
                        if (payload.filterBy.value.quantiteConfirmee.code !== '') {
                            url += `confirmedQuantity.code=${payload.filterBy.value.quantiteConfirmee.code}&`
                        }
                    }

                    if (payload.filterBy.value.dateLivraison !== ''){
                        url += `confirmedDate=${payload.filterBy.value.dateLivraison}&`
                    }
                    if (payload.filterBy.value.dateLivraisonSouhaitee !== ''){
                        url += `requestedDate=${payload.filterBy.value.dateLivraisonSouhaitee}&`
                    }

                    url += `page=${payload.nPage}`

                    response = await api(url, 'GET')
                    this.productCommande = await this.updatePagination(response)
                } else {
                    let url = `/api/selling-order-products?item=/api/products/${this.productID}&`
                    if (payload.filterBy.value.client !== ''){
                        url += `order.customer.name=${payload.filterBy.value.client}&`
                    }
                    if (payload.filterBy.value.reference !== ''){
                        url += `ref=${payload.filterBy.value.reference}&`
                    }

                    if (payload.filterBy.value.quantiteSouhaitee !== ''){
                        if (payload.filterBy.value.quantiteSouhaitee.value !== '') {
                            url += `requestedQuantity.value=${payload.filterBy.value.quantiteSouhaitee.value}&`
                        }
                        if (payload.filterBy.value.quantiteSouhaitee.code !== '') {
                            url += `requestedQuantity.code=${payload.filterBy.value.quantiteSouhaitee.code}&`
                        }
                    }

                    if (payload.filterBy.value.quantiteConfirmee !== ''){
                        if (payload.filterBy.value.quantiteConfirmee.value !== '') {
                            url += `confirmedQuantity.value=${payload.filterBy.value.quantiteConfirmee.value}&`
                        }
                        if (payload.filterBy.value.quantiteConfirmee.code !== '') {
                            url += `confirmedQuantity.code=${payload.filterBy.value.quantiteConfirmee.code}&`
                        }
                    }

                    if (payload.filterBy.value.dateLivraison !== ''){
                        url += `confirmedDate=${payload.filterBy.value.dateLivraison}&`
                    }
                    if (payload.filterBy.value.dateLivraisonSouhaitee !== ''){
                        url += `requestedDate=${payload.filterBy.value.dateLivraisonSouhaitee}&`
                    }
                    response = await api(url, 'GET')

                    this.productCommande = await this.updatePagination(response)
                }
            } else if (payload.filter.value === true){
                let url = `/api/selling-order-products?item=/api/products/${this.productID}&`
                if (payload.filterBy.value.client !== ''){
                    url += `order.customer.name=${payload.filterBy.value.client}&`
                }
                if (payload.filterBy.value.reference !== ''){
                    url += `ref=${payload.filterBy.value.reference}&`
                }

                if (payload.filterBy.value.quantiteSouhaitee !== ''){
                    if (payload.filterBy.value.quantiteSouhaitee.value !== '') {
                        url += `requestedQuantity.value=${payload.filterBy.value.quantiteSouhaitee.value}&`
                    }
                    if (payload.filterBy.value.quantiteSouhaitee.code !== '') {
                        url += `requestedQuantity.code=${payload.filterBy.value.quantiteSouhaitee.code}&`
                    }
                }

                if (payload.filterBy.value.quantiteConfirmee !== ''){
                    if (payload.filterBy.value.quantiteConfirmee.value !== '') {
                        url += `confirmedQuantity.value=${payload.filterBy.value.quantiteConfirmee.value}&`
                    }
                    if (payload.filterBy.value.quantiteConfirmee.code !== '') {
                        url += `confirmedQuantity.code=${payload.filterBy.value.quantiteConfirmee.code}&`
                    }
                }

                if (payload.filterBy.value.dateLivraison !== ''){
                    url += `confirmedDate=${payload.filterBy.value.dateLivraison}&`
                }
                if (payload.filterBy.value.dateLivraisonSouhaitee !== ''){
                    url += `requestedDate=${payload.filterBy.value.dateLivraisonSouhaitee}&`
                }
                url += `page=${payload.nPage}`
                response = await api(url, 'GET')
                this.productCommande = await this.updatePagination(response)
            } else if (payload.sortable.value === false) {
                response = await api(`/api/selling-order-products?item=/api/products/${this.productID}&page=${payload.nPage}`, 'GET')
                this.productCommande = await this.updatePagination(response)
            } else {
                if (payload.trierAlpha.value.composant === 'component') {
                    response = await api(`/api/selling-order-products?item=/api/products/${this.productID}&order%5B${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                } else {
                    response = await api(`/api/selling-order-products?item=/api/products/${this.productID}&order%5Baddress.${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                }
                this.productCommande = await this.updatePagination(response)
            }
        },

        async sortableItems(payload, filterBy, filter) {
            let response = {}
            if (filter.value === true){
                if (payload.composant === 'component') {
                    let url = `/api/selling-order-products?item=/api/products/${this.productID}&`
                    if (filterBy.value === ''){
                        await this.fetch()
                    } else {
                        if (filterBy.value.client !== '') {
                            url += `order.customer.name=${filterBy.value.client}&`
                        }
                        if (filterBy.value.reference !== ''){
                            url += `ref=${filterBy.value.reference}&`
                        }
                        if (filterBy.value.quantiteSouhaitee !== ''){
                            if (filterBy.value.quantiteSouhaitee.value !== '') {
                                url += `requestedQuantity.value=${filterBy.value.quantiteSouhaitee.value}&`
                            }
                            if (filterBy.value.quantiteSouhaitee.code !== '') {
                                url += `requestedQuantity.code=${filterBy.value.quantiteSouhaitee.code}&`
                            }
                        }
                        if (filterBy.value.quantiteConfirmee !== ''){
                            if (filterBy.value.quantiteConfirmee.value !== '') {
                                url += `confirmedQuantity.value=${filterBy.value.quantiteConfirmee.value}&`
                            }
                            if (filterBy.value.quantiteConfirmee.code !== '') {
                                url += `confirmedQuantity.code=${filterBy.value.quantiteConfirmee.code}&`
                            }
                        }
                        if (filterBy.value.dateLivraison !== ''){
                            url += `confirmedDate=${filterBy.value.dateLivraison}&`
                        }

                        if (filterBy.value.dateLivraisonSouhaitee !== ''){
                            url += `confirmedDate=${filterBy.value.dateLivraisonSouhaitee}&`
                        }
                    }
                    url += `page=${this.currentPage}`
                    response = await api(url, 'GET')
                } else {
                    let url = `/api/selling-order-products?item=/api/products/${this.productID}&`
                    if (filterBy.value === ''){
                        await this.fetch()
                    } else {
                        if (filterBy.value.client !== '') {
                            url += `order.customer.name=${filterBy.value.client}&`
                        }
                        if (filterBy.value.reference !== ''){
                            url += `ref=${filterBy.value.reference}&`
                        }
                        if (filterBy.value.quantiteSouhaitee !== ''){
                            if (filterBy.value.quantiteSouhaitee.value !== '') {
                                url += `requestedQuantity.value=${filterBy.value.quantiteSouhaitee.value}&`
                            }
                            if (filterBy.value.quantiteSouhaitee.code !== '') {
                                url += `requestedQuantity.code=${filterBy.value.quantiteSouhaitee.code}&`
                            }
                        }
                        if (filterBy.value.quantiteConfirmee !== ''){
                            if (filterBy.value.quantiteConfirmee.value !== '') {
                                url += `confirmedQuantity.value=${filterBy.value.quantiteConfirmee.value}&`
                            }
                            if (filterBy.value.quantiteConfirmee.code !== '') {
                                url += `confirmedQuantity.code=${filterBy.value.quantiteConfirmee.code}&`
                            }
                        }
                        if (filterBy.value.dateLivraison !== ''){
                            url += `confirmedDate=${filterBy.value.dateLivraison}&`
                        }

                        if (filterBy.value.dateLivraisonSouhaitee !== ''){
                            url += `confirmedDate=${filterBy.value.dateLivraisonSouhaitee}&`
                        }
                    }
                    url += `page=${this.currentPage}`
                    response = await api(url, 'GET')
                }
                this.productCommande = await this.updatePagination(response)
            } else {
                if (payload.composant === 'component') {
                    response = await api(`/api/selling-order-products?item=/api/products/${this.productID}&order%5B${payload.composant}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                } else {
                    response = await api(`/api/selling-order-products?item=/api/products/${this.productID}&order%5B${payload.produit}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                }
                this.productCommande = await this.updatePagination(response)
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
        itemsProductCommande: state => state.productCommande.map(item => {
            const newObject = {
                '@id': item['@id'],
                client: item.customer.name,
                reference: item.ref,
                quantiteConfirmee: `${item.confirmedQuantity.value} ${item.confirmedQuantity.code}`,
                quantiteSouhaitee: `${item.requestedQuantity.value} ${item.requestedQuantity.code}`,
                dateLivraison: item.confirmedDate,
                dateLivraisonSouhaitee: item.requestedDate
            }
            return newObject
        }),
        async getOptionComposant() {
            const opt = []
            const codes = new Set()
            //todo changer
            if (this.currentPage < 1){
                this.currentPage = 1
            }
            const response = await api(`/api/selling-order-products?item=/api/products/${this.productID}&page=${this.currentPage}`, 'GET')
            for (const product of response['hydra:member']) {
                if (!codes.has(product.item.code)) {
                    opt.push({value: product.item['@id'], '@type': product.item['@type'], text: product.item.code, id: product.item.id})
                    codes.add(product.item.code)
                }
            }
            opt.sort((a, b) => {
                const textA = a.text.toLowerCase()
                const textB = b.text.toLowerCase()
                if (textA < textB) {
                    return -1
                }
                if (textA > textB) {
                    return 1
                }
                return 0
            })

            return opt.length === 0 ? [{text: 'Aucun élément'}] : opt
        }
    },
    state: () => ({
        currentPage: '',
        firstPage: '',
        lastPage: '',
        nextPage: '',
        pagination: false,
        previousPage: '',
        productCommande: [],
        productID: 0
    })
})
