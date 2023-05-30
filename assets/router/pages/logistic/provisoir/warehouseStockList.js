import api from '../../../../api'
import {defineStore} from 'pinia'

export const useWarehouseStockListStore = defineStore('warehouseStockList', {
    actions: {
        setIdWarehouse(id){
            this.warehouseID = id
        },
        async addWarehouseStock(payload){
            await api('/api/stocks', 'POST', payload)
            this.fetch()
        },
        async delated(payload){
            await api(`/api/stocks/${payload}`, 'DELETE')
            this.warehousesStock = this.warehousesStock.filter(warehouse => Number(warehouse['@id'].match(/\d+/)[0]) !== payload)
        },
        async fetch() {
            const response = await api(`/api/stocks?warehouse=${this.warehouseID}`, 'GET')
            this.warehousesStock = await this.updatePagination(response)
        },
        async filterBy(payload){
            let url = '/api/stocks?'
            if (payload.component !== '') {
                url += `component=${payload.component}&`
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
            const response = await api(`/api/stocks?page=${nPage}&warehouse=${this.warehouseID}`, 'GET')
            this.warehousesStock = await this.updatePagination(response)
        },
        async paginationSortableOrFilterItems(payload) {
            let response = {}
            if (payload.filter.value === true && payload.sortable.value === true){
                if (payload.trierAlpha.value.component === 'component') {
                    let url = `/api/stocks?order%5B${payload.trierAlpha.value.component}%5D=${payload.trierAlpha.value.trier.value}&warehouse=${this.warehouseID}&`
                    if (payload.filterBy.value.component !== '') {
                        url += `component=${payload.filterBy.value.component}&`
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
                    let url = `/api/stocks?order%5Baddress.${payload.trierAlpha.value.component}%5D=${payload.trierAlpha.value.trier.value}&`
                    if (payload.filterBy.value.component !== '') {
                        url += `component=${payload.filterBy.value.component}&`
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
                if (payload.filterBy.value.component !== '') {
                    url += `component=${payload.filterBy.value.component}&`
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
                response = await api(`/api/stocks?page=${payload.nPage}&warehouse=${this.warehouseID}`, 'GET')
                this.warehousesStock = await this.updatePagination(response)
            } else {
                if (payload.trierAlpha.value.component === 'component') {
                    response = await api(`/api/stocks?order%5B${payload.trierAlpha.value.component}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}&warehouse=${this.warehouseID}`, 'GET')
                } else {
                    response = await api(`/api/stocks?order%5Baddress.${payload.trierAlpha.value.component}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}&warehouse=${this.warehouseID}`, 'GET')
                }
                this.warehousesStock = await this.updatePagination(response)
            }
        },
        async sortableItems(payload, filterBy, filter) {
            let response = {}
            if (filter.value === true){
                if (payload.component === 'component') {
                    let url = `/api/stocks?order%5B${payload.component}%5D=${payload.trier.value}&`
                    if (filterBy.value.component !== '') {
                        url += `component=${filterBy.value.component}&`
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
                    let url = `/api/stocks?order%5B${payload.component}%5D=${payload.trier.value}&warehouse=${this.warehouseID}&`
                    if (filterBy.value.component !== '') {
                        url += `component=${filterBy.value.component}&`
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
                if (payload.component === 'component') {
                    response = await api(`/api/stocks?order%5B${payload.component}%5D=${payload.trier.value}&page=${this.currentPage}&warehouse=${this.warehouseID}`, 'GET')
                } else {
                    response = await api(`/api/stocks?order%5B${payload.component}%5D=${payload.trier.value}&page=${this.currentPage}&warehouse=${this.warehouseID}`, 'GET')
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
            let prod = null
            let comp = null
            if (item['@type'] === 'ProductStock'){
                prod = item.item.code
            } else {
                comp = item.item.code
            }
            const numSerie = item.batchNumber
            const loc = item.location
            const quant = item.quantity
            const pris = item.jail
            const newObject = {
                ...item,
                composant: comp,
                produit: prod,
                numeroDeSerie: numSerie,
                localisation: loc,
                quantite: quant,
                prison: pris
            }
            return newObject
        }),
        getOptionComposant() {
            const opt = []
            const codes = new Set()

            for (const warehouse of this.warehousesStock) {
                if (warehouse['@type'] === 'ComponentStock' && !codes.has(warehouse.item.code)) {
                    opt.push({text: warehouse.item.code})
                    codes.add(warehouse.item.code)
                }
            }
            return opt.length === 0 ? [{text: 'Aucun élément'}] : opt
        },
        getOptionProduit() {
            const opt = []
            const codes = new Set()

            for (const warehouse of this.warehousesStock) {
                if (warehouse['@type'] === 'ProductStock' && !codes.has(warehouse.item.code)) {
                    opt.push({text: warehouse.item.code})
                    codes.add(warehouse.item.code)
                }
            }

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
        warehousesStock: [],
        warehouseID: 0
    })
})
