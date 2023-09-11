import api from '../../../api'
import {defineStore} from 'pinia'

export const useStockListStore = defineStore('stockList', {
    actions: {
        async addComponentStock(payload) {
            console.log('addComponentStock')
            this.violations = []
            try {
                await api('/api/component-stocks', 'POST', payload)
                console.log('création componentStock OK ?')
            } catch (error) {
                this.violations.push({message: error})
            }
        },
        async addProductStock(payload) {
            console.log('addProductStock')
            this.violations = []
            try {
                await api('/api/product-stocks', 'POST', payload)
                console.log('création productStock OK ?')
            } catch (error) {
                this.violations.push({message: error})
            }
        },
        async addStock(payload) {
            console.log('addStock')
            if (payload.component !== null && payload.product !== null) throw new Error('Il est interdit de créer un stock composant et produit à la fois')
            if (payload.component === null && payload.product === null) throw new Error('Un stock doit obligatoirement être associé à un composant ou à un produit')
            if (payload.component !== null) await this.addComponentStock(payload)
            if (payload.product !== null) await this.addProductStock(payload)
        },
        async deleted(idStock) {
            await api(`/api/stocks/${idStock}`, 'DELETE')
        },
        async fetch(fetchCriteria = '?page=1') {
            const response = await api(`/api/stocks${fetchCriteria}`, 'GET')
            this.warehousesStock = await this.updatePagination(response)
        },
        async filterBy(payload) {
            let url = '/api/'
            if (payload === ''){
                await this.fetch()
            } else {
                if (payload.composant !== '') {
                    url += `component-stocks?warehouse=${this.warehouseID}&item=${payload.composant}&`
                }
                if (payload.produit !== '') {
                    url += `product-stocks?warehouse=${this.warehouseID}&item=${payload.produit}&`
                }

                if (payload.composant === '' && payload.produit === ''){
                    url += `stocks?warehouse=${this.warehouseID}&`
                }

                if (payload.numeroDeSerie !== '') {
                    url += `batchNumber=${payload.numeroDeSerie}&`
                }
                if (payload.localisation !== '') {
                    url += `location=${payload.localisation}&`
                }
                if (payload.prison !== '') {
                    if (payload.prison === true){
                        url += 'jail=1&'
                    } else {
                        url += 'jail=0&'
                    }
                }

                if (payload.quantite !== ''){
                    if (payload.quantite.value !== '') {
                        url += `quantity.value=${payload.quantite.value}&`
                    }
                    if (payload.quantite.code !== '') {
                        url += `quantity.code=${payload.quantite.code}&`
                    }
                }
                url += 'page=1'
                this.currentPage = 1
                const response = await api(url, 'GET')
                this.warehousesStock = await this.updatePagination(response)
            }
        },
        async itemsPagination(nPage) {
            const response = await api(`/api/stocks?warehouse=${this.warehouseID}&page=${nPage}`, 'GET')
            this.warehousesStock = await this.updatePagination(response)
        },
        async paginationSortableOrFilterItems(payload) {
            let response = {}
            if (payload.filter.value === true && payload.sortable.value === true){
                if (payload.trierAlpha.value.component === 'component') {
                    let url = '/api/'
                    if (payload.filterBy.value.composant !== '') {
                        url += `component-stocks?warehouse=${this.warehouseID}&order%5B${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&item=${payload.filterBy.value.composant}&`
                    }
                    if (payload.filterBy.value.produit !== '') {
                        url += `product-stocks?warehouse=${this.warehouseID}&order%5B${payload.trierAlpha.value.produit}%5D=${payload.trierAlpha.value.trier.value}&item=${payload.filterBy.value.produit}&`
                    }
                    if (payload.filterBy.value.composant === '' && payload.filterBy.value.produit === ''){
                        url += `stocks?warehouse=${this.warehouseID}&order%5B${payload.trierAlpha.value.component}%5D=${payload.trierAlpha.value.trier.value}&`
                    }
                    if (payload.filterBy.value.numeroDeSerie !== '') {
                        url += `batchNumber=${payload.filterBy.value.numeroDeSerie}&`
                    }
                    if (payload.filterBy.value.localisation !== '') {
                        url += `location=${payload.filterBy.value.localisation}&`
                    }
                    if (payload.filterBy.value.prison !== '') {
                        if (payload.filterBy.value.prison === true){
                            url += 'jail=1&'
                        } else {
                            url += 'jail=0&'
                        }
                    }
                    if (payload.filterBy.value.quantite !== ''){
                        if (payload.filterBy.value.quantite.value !== '') {
                            url += `quantity.value=${payload.filterBy.value.quantite.value}&`
                        }
                        if (payload.filterBy.value.quantite.code !== '') {
                            url += `quantity.code=${payload.filterBy.value.quantite.code}&`
                        }
                    }
                    url += `page=${payload.nPage}`
                    response = await api(url, 'GET')
                    this.warehousesStock = await this.updatePagination(response)
                } else {
                    let url = '/api/'
                    if (payload.filterBy.value.composant !== '') {
                        url += `component-stocks?warehouse=${this.warehouseID}&order%5Baddress.${payload.trierAlpha.value.component}%5D=${payload.trierAlpha.value.trier.value}&item=${payload.filterBy.value.composant}&`
                    }
                    if (payload.filterBy.value.produit !== '') {
                        url += `product-stocks?warehouse=${this.warehouseID}&order%5Baddress.${payload.trierAlpha.value.component}%5D=${payload.trierAlpha.value.trier.value}item=${payload.filterBy.value.produit}&`
                    }
                    if (payload.filterBy.value.composant === '' && payload.filterBy.value.produit === ''){
                        url += `stocks?warehouse=${this.warehouseID}&order%5Baddress.${payload.trierAlpha.value.component}%5D=${payload.trierAlpha.value.trier.value}&`
                    }
                    if (payload.filterBy.value.numeroDeSerie !== '') {
                        url += `batchNumber=${payload.filterBy.value.numeroDeSerie}&`
                    }
                    if (payload.filterBy.value.localisation !== '') {
                        url += `location=${payload.filterBy.value.localisation}&`
                    }
                    if (payload.filterBy.value.prison !== '') {
                        if (payload.filterBy.value.prison === true){
                            url += 'jail=1&'
                        } else {
                            url += 'jail=0&'
                        }
                    }
                    if (payload.filterBy.value.quantite !== ''){
                        if (payload.filterBy.value.quantite.value !== '') {
                            url += `quantity.value=${payload.filterBy.value.quantite.value}&`
                        }
                        if (payload.filterBy.value.quantite.code !== '') {
                            url += `quantity.code=${payload.filterBy.value.quantite.code}&`
                        }
                    }
                    response = await api(url, 'GET')
                    this.warehousesStock = await this.updatePagination(response)
                }
            } else if (payload.filter.value === true){
                let url = '/api/'
                if (payload.filterBy.value.composant !== '') {
                    url += `component-stocks?warehouse=${this.warehouseID}&item=${payload.filterBy.value.composant}&`
                }
                if (payload.filterBy.value.produit !== '') {
                    url += `product-stocks?warehouse=${this.warehouseID}&item=${payload.filterBy.value.produit}&`
                }
                if (payload.filterBy.value.composant === '' && payload.filterBy.value.produit === ''){
                    url += `stocks?warehouse=${this.warehouseID}&`
                }
                if (payload.filterBy.value.numeroDeSerie !== '') {
                    url += `batchNumber=${payload.filterBy.value.numeroDeSerie}&`
                }
                if (payload.filterBy.value.localisation !== '') {
                    url += `location=${payload.filterBy.value.localisation}&`
                }
                if (payload.filterBy.value.prison !== '') {
                    if (payload.filterBy.value.prison === true){
                        url += 'jail=1&'
                    } else {
                        url += 'jail=0&'
                    }
                }
                if (payload.filterBy.value.quantite !== ''){
                    if (payload.filterBy.value.quantite.value !== '') {
                        url += `quantity.value=${payload.filterBy.value.quantite.value}&`
                    }
                    if (payload.filterBy.value.quantite.code !== '') {
                        url += `quantity.code=${payload.filterBy.value.quantite.code}&`
                    }
                }
                url += `page=${payload.nPage}`
                response = await api(url, 'GET')
                this.warehousesStock = await this.updatePagination(response)
            } else if (payload.sortable.value === false) {
                response = await api(`/api/stocks?warehouse=${this.warehouseID}&page=${payload.nPage}`, 'GET')
                this.warehousesStock = await this.updatePagination(response)
            } else {
                if (payload.trierAlpha.value.composant === 'component') {
                    response = await api(`/api/stocks?warehouse=${this.warehouseID}&order%5B${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                } else {
                    response = await api(`/api/stocks?warehouse=${this.warehouseID}&order%5Baddress.${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                }
                this.warehousesStock = await this.updatePagination(response)
            }
        },

        async sortableItems(payload, filterBy, filter) {
            let response = {}
            if (filter.value === true){
                if (payload.composant === 'component') {
                    let url = '/api/'
                    if (filterBy.value.composant !== '') {
                        url += `component-stocks?warehouse=${this.warehouseID}&order%5B${filterBy.value.composant}%5D=${payload.trier.value}&item=${filterBy.value.composant}&`
                    }
                    if (filterBy.value.produit !== '') {
                        url += `product-stocks?warehouse=${this.warehouseID}&order%5B${filterBy.value.produit}%5D=${payload.trier.value}&item=${filterBy.value.produit}&`
                    }
                    if (filterBy.value.composant === '' && filterBy.value.produit === ''){
                        url += `stocks?warehouse=${this.warehouseID}&order%5B${filterBy.value.composant}%5D=${payload.trier.value}&`
                    }
                    if (filterBy.value.numeroDeSerie !== '') {
                        url += `batchNumber=${filterBy.value.numeroDeSerie}&`
                    }
                    if (filterBy.value.localisation !== '') {
                        url += `location=${filterBy.value.localisation}&`
                    }
                    if (filterBy.value.prison !== '') {
                        if (filterBy.value.prison === true){
                            url += 'jail=1&'
                        } else {
                            url += 'jail=0&'
                        }
                    }
                    if (filterBy.value.quantite !== ''){
                        if (filterBy.value.quantite.value !== '') {
                            url += `quantity.value=${filterBy.value.quantite.value}&`
                        }
                        if (filterBy.value.quantite.code !== '') {
                            url += `quantity.code=${filterBy.value.quantite.code}&`
                        }
                    }
                    url += `page=${this.currentPage}`
                    response = await api(url, 'GET')
                } else {
                    let url = '/api/'
                    if (filterBy.value.composant !== '') {
                        url += `component-stocks?warehouse=${this.warehouseID}&order%5B${filterBy.value.composant}%5D=${payload.trier.value}&item=${filterBy.value.composant}&`
                    }
                    if (filterBy.value.produit !== '') {
                        url += `product-stocks?warehouse=${this.warehouseID}&order%5B${filterBy.value.produit}%5D=${payload.trier.value}&item=${filterBy.value.produit}&`
                    }
                    if (filterBy.value.composant === '' && filterBy.value.produit === ''){
                        url += `stocks?warehouse=${this.warehouseID}&order%5B${filterBy.value.composant}%5D=${payload.trier.value}&`
                    }
                    if (filterBy.value.numeroDeSerie !== '') {
                        url += `batchNumber=${filterBy.value.numeroDeSerie}&`
                    }
                    if (filterBy.value.localisation !== '') {
                        url += `location=${filterBy.value.localisation}&`
                    }
                    if (filterBy.value.prison !== '') {
                        if (filterBy.value.prison === true){
                            url += 'jail=1&'
                        } else {
                            url += 'jail=0&'
                        }
                    }
                    if (filterBy.value.quantite !== ''){
                        if (filterBy.value.quantite.value !== '') {
                            url += `quantity.value=${filterBy.value.quantite.value}&`
                        }
                        if (filterBy.value.quantite.code !== '') {
                            url += `quantity.code=${filterBy.value.quantite.code}&`
                        }
                    }
                    url += `page=${this.currentPage}`
                    response = await api(url, 'GET')
                }
                this.warehousesStock = await this.updatePagination(response)
            } else {
                if (payload.composant === 'component') {
                    response = await api(`/api/stocks?warehouse=${this.warehouseID}&order%5B${payload.composant}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                } else {
                    response = await api(`/api/stocks?warehouse=${this.warehouseID}&order%5B${payload.produit}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
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
            this.fetch()
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
        async getOptionComposant() {
            const opt = []
            const codes = new Set()
            const response = await api(`/api/component-stocks?warehouse=${this.warehouseID}&pagination=false`, 'GET')

            for (const warehouse of response['hydra:member']) {
                if (warehouse['@type'] === 'ComponentStock' && !codes.has(warehouse.item.code)) {
                    opt.push({value: warehouse.item['@id'], '@type': warehouse.item['@type'], text: warehouse.item.code, id: warehouse.item.id})
                    codes.add(warehouse.item.code)
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
        },
        async getOptionProduit() {
            const opt = []
            const codes = new Set()
            const response = await api(`/api/product-stocks?warehouse=${this.warehouseID}&pagination=false`, 'GET')

            for (const warehouse of response['hydra:member']) {
                if (warehouse['@type'] === 'ProductStock' && !codes.has(warehouse.item.code)) {
                    opt.push({value: warehouse.item['@id'], '@type': warehouse.item['@type'], text: warehouse.item.code, id: warehouse.item.id})
                    codes.add(warehouse.item.code)
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
        warehousesStock: [],
        warehouseID: 0,
        stocks: [],
        componentStocks: [],
        productStocks: [],
        componentStock: {},
        productStock: {},
        stock: {},
        violations: []
    })
})
