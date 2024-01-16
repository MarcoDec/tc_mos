import api from '../../../api'
import {defineStore} from 'pinia'

export const useComponentListStockStore = defineStore('componentListStock', {
    actions: {
        setIdComponent(id){
            this.componentID = id
        },
        // async addStockStock(payload){
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
        // async deleted(payload) {
        //     await api(`/api/component-stocks/${payload}`, 'DELETE')
        //     this.componentStock = this.componentStock.filter(retard => Number(retard['@id'].match(/\d+/)[0]) !== payload)
        // },
        async fetch() {
            if (this.currentPage < 1){
                this.currentPage = 1
            }
            const response = await api(`/api/component-stocks/filtreComponentTotalQuantite/${this.componentID}`, 'GET')
            this.componentStock = await this.updatePagination(response)
        },
        async filterBy(payload) {
            let url = `/api/component-stocks/filtreComponentTotalQuantite/${this.componentID}?`
            if (payload === ''){
                await this.fetch()
            } else {
                if (payload.name !== '') {
                    url += `name=${payload.name}&`
                }
                if (payload.etat !== '') {
                    if (payload.etat === true){
                        url += 'jail=1&'
                    } else {
                        url += 'jail=0&'
                    }
                }

                if (payload.quantiteDispo !== ''){
                    if (payload.quantiteDispo.value !== '') {
                        url += `quantiteValue=${payload.quantiteDispo.value}&`
                    }
                    if (payload.quantiteDispo.code !== '') {
                        url += `quantiteCode=${payload.quantiteDispo.code}&`
                    }
                }

                url += 'page=1'
                this.currentPage = 1
                const response = await api(url, 'GET')
                this.componentStock = await this.updatePagination(response)
            }
        },
        async itemsPagination(nPage) {
            const response = await api(`/api/component-stocks/filtreComponentTotalQuantite/${this.componentID}&page=${nPage}`, 'GET')
            this.componentStock = await this.updatePagination(response)
        },
        async paginationSortableOrFilterItems(payload) {
            let response = {}
            if (payload.filter.value === true && payload.sortable.value === true){
                let url = `/api/component-stocks/filtreComponentTotalQuantite/${this.componentID}&`
                if (payload.filterBy.value.name !== '') {
                    url += `name=${payload.filterBy.value.name}&`
                }
                if (payload.filterBy.value.etat !== '') {
                    if (payload.filterBy.value.etat === true){
                        url += 'jail=1&'
                    } else {
                        url += 'jail=0&'
                    }
                }

                if (payload.filterBy.value.quantiteDispo !== ''){
                    if (payload.filterBy.value.quantiteDispo.value !== '') {
                        url += `quantiteValue=${payload.filterBy.value.quantiteDispo.value}&`
                    }
                    if (payload.filterBy.value.quantiteDispo.code !== '') {
                        url += `quantiteCode=${payload.filterBy.value.quantiteDispo.code}&`
                    }
                }

                response = await api(url, 'GET')
                this.componentStock = await this.updatePagination(response)
            } else if (payload.filter.value === true){
                let url = `/api/component-stocks/filtreComponentTotalQuantite/${this.componentID}&`
                if (payload.filterBy.value.name !== '') {
                    url += `name=${payload.filterBy.value.name}&`
                }
                if (payload.filterBy.value.etat !== '') {
                    if (payload.filterBy.value.etat === true){
                        url += 'jail=1&'
                    } else {
                        url += 'jail=0&'
                    }
                }

                if (payload.filterBy.value.quantiteDispo !== ''){
                    if (payload.filterBy.value.quantiteDispo.value !== '') {
                        url += `quantiteValue=${payload.filterBy.value.quantiteDispo.value}&`
                    }
                    if (payload.filterBy.value.quantiteDispo.code !== '') {
                        url += `quantiteCode=${payload.filterBy.value.quantiteDispo.code}&`
                    }
                }
                url += `page=${payload.nPage}`
                response = await api(url, 'GET')
                this.componentStock = await this.updatePagination(response)
            } else if (payload.sortable.value === false) {
                response = await api(`/api/component-stocks/filtreComponentTotalQuantite/${this.componentID}&page=${payload.nPage}`, 'GET')
                this.componentStock = await this.updatePagination(response)
            } else {
                if (payload.trierAlpha.value.composant === 'component') {
                    response = await api(`/api/component-stocks/filtreComponentTotalQuantite/${this.componentID}&order%5B${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                } else {
                    response = await api(`/api/component-stocks/filtreComponentTotalQuantite/${this.componentID}&order%5Baddress.${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                }
                this.componentStock = await this.updatePagination(response)
            }
        },

        async sortableItems(payload, filterBy, filter) {
            let response = {}
            if (filter.value === true){
                if (filterBy.value.name !== '') {
                    url += `name=${filterBy.value.name}&`
                }
                if (filterBy.value.etat !== '') {
                    if (filterBy.value.etat === true){
                        url += 'jail=1&'
                    } else {
                        url += 'jail=0&'
                    }
                }

                if (filterBy.value.quantiteDispo !== ''){
                    if (filterBy.value.quantiteDispo.value !== '') {
                        url += `quantiteValue=${filterBy.value.quantiteDispo.value}&`
                    }
                    if (filterBy.value.quantiteDispo.code !== '') {
                        url += `quantiteCode=${filterBy.value.quantiteDispo.code}&`
                    }
                }
                url += `page=${this.currentPage}`
                response = await api(url, 'GET')
                this.componentStock = await this.updatePagination(response)
            } else {
                if (payload.composant === 'component') {
                    response = await api(`/api/component-stocks/filtreComponentTotalQuantite/${this.componentID}&order%5B${payload.composant}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                } else {
                    response = await api(`/api/component-stocks/filtreComponentTotalQuantite/${this.componentID}&order%5B${payload.produit}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                }
                this.componentStock = await this.updatePagination(response)
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
        itemsComponentStock: state => state.componentStock.map(item => {
            const newObject = {
                '@id': item['@id'],
                name: item.name,
                etat: item.jail,
                quantiteEnregistre: null,
                quantiteDispo: `${item.quantiteValue} ${item.quantiteCode}`
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
        componentStock: [],
        componentID: 0
    })
})
