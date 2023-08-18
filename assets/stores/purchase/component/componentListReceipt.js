import api from '../../../api'
import {defineStore} from 'pinia'

export const useComponentListReceiptStore = defineStore('componentListReceipt', {
    actions: {
        setIdComponent(id){
            this.componentID = id
        },
        getUniqueProductCodes(response) {
            const uniqueCodesSet = new Set()
            if (response && response['hydra:member']) {
                response['hydra:member'].forEach(item => {
                    if (item.product.product && item.productReceipt) {
                        uniqueCodesSet.add(item.productReceipt)
                    }
                })
            }
            return Array.from(uniqueCodesSet)
        },
        // async addReceiptReceipt(payload){
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
            this.componentReceipt = this.componentReceipt.filter(retard => Number(retard['@id'].match(/\d+/)[0]) !== payload)
        },
        async fetch() {
            if (this.currentPage < 1){
                this.currentPage = 1
            }
            const response = await api(`/api/manufacturing-orders/componentFilter/${this.componentID}?page=${this.currentPage}`, 'GET')
            this.componentReceipt = await this.updatePagination(response)
        },
        async filterBy(payload) {
            let url = `/api/manufacturing-orders/componentFilter/${this.componentID}&`
            if (payload === ''){
                await this.fetch()
            } else {
                if (payload.of !== '') {
                    url += `productReceipt=${payload.of}&`
                }
                if (payload.produit !== '') {
                    url += `name=${payload.produit}&`
                }
                if (payload.date !== '') {
                    url += `date=${payload.date}&`
                }
                if (payload.quantiteComposant !== ''){
                    if (payload.quantiteComposant.value !== '') {
                        url += `forecastVolumeValue=${payload.quantiteComposant.value}&`
                    }
                    if (payload.quantiteComposant.code !== '') {
                        url += `forecastVolumeCode=${payload.quantiteComposant.code}&`
                    }
                }
                url += 'page=1'
                this.currentPage = 1
                const response = await api(url, 'GET')
                this.componentReceipt = await this.updatePagination(response)
            }
        },
        async itemsPagination(nPage) {
            const response = await api(`/api/manufacturing-orders/componentFilter/${this.componentID}?page=${this.currentPage}&page=${nPage}`, 'GET')
            this.componentReceipt = await this.updatePagination(response)
        },
        async paginationSortableOrFilterItems(payload) {
            let response = {}
            if (payload.filter.value === true && payload.sortable.value === true){
                let url = `/api/manufacturing-orders/componentFilter/${this.componentID}?page=${this.currentPage}&`
                if (payload.filterBy.value.of !== '') {
                    url += `productReceipt=${payload.filterBy.value.of}&`
                }
                if (payload.filterBy.value.produit !== '') {
                    url += `name=${payload.filterBy.value.produit}&`
                }
                if (payload.filterBy.value.date !== '') {
                    url += `date=${payload.filterBy.value.date}&`
                }
                if (payload.filterBy.value.quantiteComposant !== ''){
                    if (payload.filterBy.value.quantiteComposant.value !== '') {
                        url += `forecastVolumeValue=${payload.filterBy.value.quantiteComposant.value}&`
                    }
                    if (payload.filterBy.value.quantiteComposant.code !== '') {
                        url += `forecastVolumeCode=${payload.filterBy.value.quantiteComposant.code}&`
                    }
                }

                response = await api(url, 'GET')
                this.componentReceipt = await this.updatePagination(response)
            } else if (payload.filter.value === true){
                let url = `/api/manufacturing-orders/componentFilter/${this.componentID}?page=${this.currentPage}&`
                if (payload.filterBy.value.of !== '') {
                    url += `productReceipt=${payload.filterBy.value.of}&`
                }
                if (payload.filterBy.value.produit !== '') {
                    url += `name=${payload.filterBy.value.produit}&`
                }
                if (payload.filterBy.value.date !== '') {
                    url += `date=${payload.filterBy.value.date}&`
                }
                if (payload.filterBy.value.quantiteComposant !== ''){
                    if (payload.filterBy.value.quantiteComposant.value !== '') {
                        url += `forecastVolumeValue=${payload.filterBy.value.quantiteComposant.value}&`
                    }
                    if (payload.filterBy.value.quantiteComposant.code !== '') {
                        url += `forecastVolumeCode=${payload.filterBy.value.quantiteComposant.code}&`
                    }
                }

                url += `page=${payload.nPage}`
                response = await api(url, 'GET')
                this.componentReceipt = await this.updatePagination(response)
            } else if (payload.sortable.value === false) {
                response = await api(`/api/manufacturing-orders/componentFilter/${this.componentID}?page=${this.currentPage}&page=${payload.nPage}`, 'GET')
                this.componentReceipt = await this.updatePagination(response)
            } else {
                if (payload.trierAlpha.value.composant === 'component') {
                    response = await api(`/api/manufacturing-orders/componentFilter/${this.componentID}?page=${this.currentPage}&order%5B${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                } else {
                    response = await api(`/api/manufacturing-orders/componentFilter/${this.componentID}?page=${this.currentPage}&order%5Baddress.${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                }
                this.componentReceipt = await this.updatePagination(response)
            }
        },

        async sortableItems(payload, filterBy, filter) {
            let response = {}
            if (filter.value === true){
                if (filterBy.value.of !== '') {
                    url += `productReceipt=${filterBy.value.of}&`
                }
                if (payload.filterBy.value.produit !== '') {
                    url += `name=${filterBy.value.produit}&`
                }
                if (filterBy.value.date !== '') {
                    url += `date=${filterBy.value.date}&`
                }
                if (filterBy.value.quantiteComposant !== ''){
                    if (filterBy.value.quantiteComposant.value !== '') {
                        url += `forecastVolumeValue=${filterBy.value.quantiteComposant.value}&`
                    }
                    if (filterBy.value.quantiteComposant.code !== '') {
                        url += `forecastVolumeCode=${filterBy.value.quantiteComposant.code}&`
                    }
                }
                url += `page=${this.currentPage}`
                response = await api(url, 'GET')
                this.componentReceipt = await this.updatePagination(response)
            } else {
                if (payload.composant === 'component') {
                    response = await api(`/api/manufacturing-orders/componentFilter/${this.componentID}?page=${this.currentPage}&order%5B${payload.composant}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                } else {
                    response = await api(`/api/manufacturing-orders/componentFilter/${this.componentID}?page=${this.currentPage}&order%5B${payload.produit}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                }
                this.componentReceipt = await this.updatePagination(response)
            }
        },
        async updatePagination(response) {
            const responseData = await response['hydra:member']
            if (responseData.length === 3 && responseData[1] > 1) {
                this.pagination = true
                this.firstPage = 1
                this.currentPage = responseData[0]
                this.lastPage = responseData[1]
                if (this.currentPage >= this.lastPage){
                    this.nextPage = this.lastPage
                } else {
                    this.nextPage = parseInt(responseData[0]) + 1
                }
                if (this.currentPage <= this.firstPage) {
                    this.previousPage = this.firstPage
                } else {
                    this.previousPage = this.currentPage - 1
                }
                return responseData[2]
            }
            this.pagination = false
            return responseData[2]
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
        itemsComponentReceipt: state => state.componentReceipt.map(item => {
            const newObject = {
                '@id': item['@id'],
                of: item.ref,
                produit: item.name,
                date: item.date,
                quantiteComposant: `${item.forecastVolumeValue} ${item.forecastVolumeCode}`
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
        componentReceipt: [],
        componentID: 0
    })
})
