import api from '../../../../api'
import {defineStore} from 'pinia'

export const useWarehouseStockListStore = defineStore('warehouseStockList', {
    actions: {
        async addWarehouseStock(payload){
            await api('/api/stocks', 'POST', payload)
            //this.itemsPagination(this.lastPage)
            this.fetch()
        },

        async delated(payload){
            await api(`/api/stocks/${payload}`, 'DELETE')
            this.warehousesStock = this.warehousesStock.filter(warehouse => Number(warehouse['@id'].match(/\d+/)[0]) !== payload)
        },
        async fetch() {
            const response = await api('/api/stocks', 'GET')
            this.warehousesStock = await this.updatePagination(response)
            //console.log('creê')
        },
        async filterBy(payload){
            let url = '/api/stocks?'
            if (payload.composant !== '') {
                url += `composant=${payload.composant}&`
            }
            // if (payload.porduit !== '') {
            //     url += `produit=${payload.produit}&`
            // }
            // if (payload.numeroDeSerie !== '') {
            //     url += `numeroDeSerie=${payload.numeroDeSerie}&`
            // }
            // if (payload.localisation !== '') {
            //     url += `localisation=${payload.localisation}&`
            // }
            // if (payload.quantite !== '') {
            //     url += `quantite=${payload.quantite}&`
            // }
            url += 'page=1'
            const response = await api(url, 'GET')
            this.warehousesStock = await this.updatePagination(response)
        },
        async itemsPagination(nPage) {
            const response = await api(`/api/stocks?page=${nPage}`, 'GET')
            this.warehousesStock = await this.updatePagination(response)
        },
        async paginationSortableOrFilterItems(payload) {
            let response = {}
            if (payload.filter.value === true && payload.sortable.value === true){
                if (payload.trierAlpha.value.composant === 'composant') {
                    let url = `/api/stocks?order%5B${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&`
                    if (payload.filterBy.value.composant !== '') {
                        url += `composant=${payload.filterBy.value.composant}&`
                    }
                    // if (payload.porduit !== '') {
                    //     url += `produit=${payload.produit}&`
                    // }
                    // if (payload.numeroDeSerie !== '') {
                    //     url += `numeroDeSerie=${payload.numeroDeSerie}&`
                    // }
                    // if (payload.localisation !== '') {
                    //     url += `localisation=${payload.localisation}&`
                    // }
                    // if (payload.quantite !== '') {
                    //     url += `quantite=${payload.quantite}&`
                    // }
                    url += `page=${payload.nPage}`
                    response = await api(url, 'GET')
                    this.societies = await this.updatePagination(response)
                } else {
                    let url = `/api/stocks?order%5Baddress.${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&`
                    if (payload.filterBy.value.composant !== '') {
                        url += `composant=${payload.filterBy.value.composant}&`
                    }
                    // if (payload.porduit !== '') {
                    //     url += `produit=${payload.produit}&`
                    // }
                    // if (payload.numeroDeSerie !== '') {
                    //     url += `numeroDeSerie=${payload.numeroDeSerie}&`
                    // }
                    // if (payload.localisation !== '') {
                    //     url += `localisation=${payload.localisation}&`
                    // }
                    // if (payload.quantite !== '') {
                    //     url += `quantite=${payload.quantite}&`
                    // }
                    // if (payload.filterBy.value.address.families !== '') {
                    //     url += `address.families=${payload.filterBy.value.address.families}&`
                    // }
                    // url += `page=${payload.nPage}`
                    response = await api(url, 'GET')
                    this.warehousesStock = await this.updatePagination(response)
                }
            } else if (payload.filter.value === true){
                let url = '/api/stocks?'
                if (payload.filterBy.value.composant !== '') {
                    url += `composant=${payload.filterBy.value.composant}&`
                }
                // if (payload.porduit !== '') {
                //     url += `produit=${payload.produit}&`
                // }
                // if (payload.numeroDeSerie !== '') {
                //     url += `numeroDeSerie=${payload.numeroDeSerie}&`
                // }
                // if (payload.localisation !== '') {
                //     url += `localisation=${payload.localisation}&`
                // }
                // if (payload.quantite !== '') {
                //     url += `quantite=${payload.quantite}&`
                // }
                // if (payload.filterBy.value.address.families !== '') {
                //     url += `address.families=${payload.filterBy.value.address.families}&`
                // }
                url += `page=${payload.nPage}`
                response = await api(url, 'GET')
                this.warehousesStock = await this.updatePagination(response)
            } else if (payload.sortable.value === false) {
                response = await api(`/api/stocks?page=${payload.nPage}`, 'GET')
                this.warehousesStock = await this.updatePagination(response)
            } else {
                if (payload.trierAlpha.value.composant === 'composant') {
                    response = await api(`/api/stocks?order%5B${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                } else {
                    response = await api(`/api/stocks?order%5Baddress.${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                }
                this.warehousesStock = await this.updatePagination(response)
            }
        },
        async sortableItems(payload, filterBy, filter) {
            let response = {}
            if (filter.value === true){
                if (payload.composant === 'composant') {
                    let url = `/api/stocks?order%5B${payload.composant}%5D=${payload.trier.value}&`
                    if (filterBy.value.composant !== '') {
                        url += `composant=${filterBy.value.composant}&`
                    }
                    // if (payload.porduit !== '') {
                    //     url += `produit=${payload.produit}&`
                    // }
                    // if (payload.numeroDeSerie !== '') {
                    //     url += `numeroDeSerie=${payload.numeroDeSerie}&`
                    // }
                    // if (payload.localisation !== '') {
                    //     url += `localisation=${payload.localisation}&`
                    // }
                    // if (payload.quantite !== '') {
                    //     url += `quantite=${payload.quantite}&`
                    // }
                    url += `page=${this.currentPage}`
                    response = await api(url, 'GET')
                } else {
                    let url = `/api/stocks?order%5B${payload.composant}%5D=${payload.trier.value}&`
                    if (filterBy.value.composant !== '') {
                        url += `composant=${filterBy.value.composant}&`
                    }
                    // if (payload.porduit !== '') {
                    //     url += `produit=${payload.produit}&`
                    // }
                    // if (payload.numeroDeSerie !== '') {
                    //     url += `numeroDeSerie=${payload.numeroDeSerie}&`
                    // }
                    // if (payload.localisation !== '') {
                    //     url += `localisation=${payload.localisation}&`
                    // }
                    // if (payload.quantite !== '') {
                    //     url += `quantite=${payload.quantite}&`
                    // }
                    url += `page=${this.currentPage}`
                    response = await api(url, 'GET')
                }
                this.warehousesStock = await this.updatePagination(response)
            } else {
                if (payload.composant === 'composant') {
                    response = await api(`/api/stocks?order%5B${payload.composant}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                } else {
                    response = await api(`/api/stocks?order%5B${payload.composant}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                }
                this.warehousesStock = await this.updatePagination(response)
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
        },
        async updateWarehouseStock(payload){
            await api(`/api/stocks/${payload.id}`, 'PATCH', payload.itemsUpdateData)
            if (payload.sortable.value === true || payload.filter.value === true) {
                this.paginationSortableOrFilterItems({filter: payload.filter, filterBy: payload.filterBy, nPage: this.currentPage, sortable: payload.sortable, trierAlpha: payload.trierAlpha})
            } else {
                this.itemsPagination(this.currentPage)
            }
        }

    },
    getters: {
        itemsWarehousesStock: state => state.warehousesStock.map(item => {
            const comp = item['@id']
            const prod = 10
            const numSerie = item.batchNumber
            const loc = 'l'
            const quant = item.quantity_value
            const pris = item.jail
            const newObject = {
                ...item,
                composant: comp,
                produit: prod,
                numeroDeSerie: numSerie,
                location: loc,
                quantite: quant,
                prison: pris
            }
            //console.log(newObject)
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
        warehousesStock: []
    })
})
