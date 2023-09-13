import api from '../../../api'
import {defineStore} from 'pinia'

export const useStockListStore = defineStore('stockList', {
    actions: {
        async addComponentStock(payload) {
            this.violations = []
            try {
                await api('/api/component-stocks', 'POST', payload)
            } catch (errors) {
                errors.forEach(error => {
                    this.violations.push({message: error.message})
                })
                throw new Error('Impossible d\'ajouter le stock composant')
            }
        },
        async addProductStock(payload) {
            this.violations = []
            try {
                await api('/api/product-stocks', 'POST', payload)
            } catch (errors) {
                // eslint-disable-next-line array-callback-return
                errors.forEach(error => this.violations.push({message: error.message}))
                throw new Error('Impossible d\'ajouter le stock produit')
            }
        },
        async addStock(payload) {
            if (payload.item !== null && payload.item.includes('components')) await this.addComponentStock(payload)
            if (payload.item !== null && payload.item.includes('products')) await this.addProductStock(payload)
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
                if (payload.component !== '') {
                    url += `component-stocks?warehouse=${this.warehouseID}&item=${payload.component}&`
                }
                if (payload.product !== '') {
                    url += `product-stocks?warehouse=${this.warehouseID}&item=${payload.product}&`
                }

                if (payload.component === '' && payload.product === ''){
                    url += `stocks?warehouse=${this.warehouseID}&`
                }

                if (payload.batchNumber !== '') {
                    url += `batchNumber=${payload.batchNumber}&`
                }
                if (payload.location !== '') {
                    url += `location=${payload.location}&`
                }
                if (payload.jail !== '') {
                    if (payload.jail === true){
                        url += 'jail=1&'
                    } else {
                        url += 'jail=0&'
                    }
                }

                if (payload.quantity !== ''){
                    if (payload.quantity.value !== '') {
                        url += `quantity.value=${payload.quantity.value}&`
                    }
                    if (payload.quantity.code !== '') {
                        url += `quantity.code=${payload.quantity.code}&`
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
                    if (payload.filterBy.value.component !== '') {
                        url += `component-stocks?warehouse=${this.warehouseID}&order%5B${payload.trierAlpha.value.component}%5D=${payload.trierAlpha.value.trier.value}&item=${payload.filterBy.value.component}&`
                    }
                    if (payload.filterBy.value.product !== '') {
                        url += `product-stocks?warehouse=${this.warehouseID}&order%5B${payload.trierAlpha.value.product}%5D=${payload.trierAlpha.value.trier.value}&item=${payload.filterBy.value.product}&`
                    }
                    if (payload.filterBy.value.component === '' && payload.filterBy.value.product === ''){
                        url += `stocks?warehouse=${this.warehouseID}&order%5B${payload.trierAlpha.value.component}%5D=${payload.trierAlpha.value.trier.value}&`
                    }
                    if (payload.filterBy.value.batchNumber !== '') {
                        url += `batchNumber=${payload.filterBy.value.batchNumber}&`
                    }
                    if (payload.filterBy.value.location !== '') {
                        url += `location=${payload.filterBy.value.location}&`
                    }
                    if (payload.filterBy.value.jail !== '') {
                        if (payload.filterBy.value.jail === true){
                            url += 'jail=1&'
                        } else {
                            url += 'jail=0&'
                        }
                    }
                    if (payload.filterBy.value.quantity !== ''){
                        if (payload.filterBy.value.quantity.value !== '') {
                            url += `quantity.value=${payload.filterBy.value.quantity.value}&`
                        }
                        if (payload.filterBy.value.quantity.code !== '') {
                            url += `quantity.code=${payload.filterBy.value.quantity.code}&`
                        }
                    }
                    url += `page=${payload.nPage}`
                    response = await api(url, 'GET')
                    this.warehousesStock = await this.updatePagination(response)
                } else {
                    let url = '/api/'
                    if (payload.filterBy.value.component !== '') {
                        url += `component-stocks?warehouse=${this.warehouseID}&order%5Baddress.${payload.trierAlpha.value.component}%5D=${payload.trierAlpha.value.trier.value}&item=${payload.filterBy.value.composant}&`
                    }
                    if (payload.filterBy.value.produit !== '') {
                        url += `product-stocks?warehouse=${this.warehouseID}&order%5Baddress.${payload.trierAlpha.value.component}%5D=${payload.trierAlpha.value.trier.value}item=${payload.filterBy.value.produit}&`
                    }
                    if (payload.filterBy.value.composant === '' && payload.filterBy.value.produit === ''){
                        url += `stocks?warehouse=${this.warehouseID}&order%5Baddress.${payload.trierAlpha.value.component}%5D=${payload.trierAlpha.value.trier.value}&`
                    }
                    if (payload.filterBy.value.batchNumber !== '') {
                        url += `batchNumber=${payload.filterBy.value.batchNumber}&`
                    }
                    if (payload.filterBy.value.location !== '') {
                        url += `location=${payload.filterBy.value.location}&`
                    }
                    if (payload.filterBy.value.jail !== '') {
                        if (payload.filterBy.value.jail === true){
                            url += 'jail=1&'
                        } else {
                            url += 'jail=0&'
                        }
                    }
                    if (payload.filterBy.value.quantity !== ''){
                        if (payload.filterBy.value.quantity.value !== '') {
                            url += `quantity.value=${payload.filterBy.value.quantity.value}&`
                        }
                        if (payload.filterBy.value.quantity.code !== '') {
                            url += `quantity.code=${payload.filterBy.value.quantity.code}&`
                        }
                    }
                    response = await api(url, 'GET')
                    this.warehousesStock = await this.updatePagination(response)
                }
            } else if (payload.filter.value === true){
                let url = '/api/'
                if (payload.filterBy.value.component !== '') {
                    url += `component-stocks?warehouse=${this.warehouseID}&item=${payload.filterBy.value.component}&`
                }
                if (payload.filterBy.value.product !== '') {
                    url += `product-stocks?warehouse=${this.warehouseID}&item=${payload.filterBy.value.product}&`
                }
                if (payload.filterBy.value.component === '' && payload.filterBy.value.product === ''){
                    url += `stocks?warehouse=${this.warehouseID}&`
                }
                if (payload.filterBy.value.batchNumber !== '') {
                    url += `batchNumber=${payload.filterBy.value.batchNumber}&`
                }
                if (payload.filterBy.value.location !== '') {
                    url += `location=${payload.filterBy.value.location}&`
                }
                if (payload.filterBy.value.jail !== '') {
                    if (payload.filterBy.value.jail === true){
                        url += 'jail=1&'
                    } else {
                        url += 'jail=0&'
                    }
                }
                if (payload.filterBy.value.quantity !== ''){
                    if (payload.filterBy.value.quantity.value !== '') {
                        url += `quantity.value=${payload.filterBy.value.quantity.value}&`
                    }
                    if (payload.filterBy.value.quantity.code !== '') {
                        url += `quantity.code=${payload.filterBy.value.quantity.code}&`
                    }
                }
                url += `page=${payload.nPage}`
                response = await api(url, 'GET')
                this.warehousesStock = await this.updatePagination(response)
            } else if (payload.sortable.value === false) {
                response = await api(`/api/stocks?warehouse=${this.warehouseID}&page=${payload.nPage}`, 'GET')
                this.warehousesStock = await this.updatePagination(response)
            } else {
                if (payload.trierAlpha.value.component === 'component') {
                    response = await api(`/api/stocks?warehouse=${this.warehouseID}&order%5B${payload.trierAlpha.value.component}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                } else {
                    response = await api(`/api/stocks?warehouse=${this.warehouseID}&order%5Baddress.${payload.trierAlpha.value.component}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                }
                this.warehousesStock = await this.updatePagination(response)
            }
        },
        async updateStock(iri, data) {
            this.violations = []
            try {
                await api(iri, 'PATCH', data)
            } catch (errors) {
                errors.forEach(error => {
                    this.violations.push({message: error.message})
                })
                throw new Error('Impossible de modifier le stock')
            }
        },
        async sortableItems(payload, filterBy, filter) {
            let response = {}
            if (filter.value === true){
                if (payload.component === 'component') {
                    let url = '/api/'
                    if (filterBy.value.component !== '') {
                        url += `component-stocks?warehouse=${this.warehouseID}&order%5B${filterBy.value.component}%5D=${payload.trier.value}&item=${filterBy.value.component}&`
                    }
                    if (filterBy.value.product !== '') {
                        url += `product-stocks?warehouse=${this.warehouseID}&order%5B${filterBy.value.product}%5D=${payload.trier.value}&item=${filterBy.value.product}&`
                    }
                    if (filterBy.value.component === '' && filterBy.value.product === ''){
                        url += `stocks?warehouse=${this.warehouseID}&order%5B${filterBy.value.component}%5D=${payload.trier.value}&`
                    }
                    if (filterBy.value.batchNumber !== '') {
                        url += `batchNumber=${filterBy.value.batchNumber}&`
                    }
                    if (filterBy.value.location !== '') {
                        url += `location=${filterBy.value.location}&`
                    }
                    if (filterBy.value.jail !== '') {
                        if (filterBy.value.jail === true){
                            url += 'jail=1&'
                        } else {
                            url += 'jail=0&'
                        }
                    }
                    if (filterBy.value.quantity !== ''){
                        if (filterBy.value.quantity.value !== '') {
                            url += `quantity.value=${filterBy.value.quantity.value}&`
                        }
                        if (filterBy.value.quantity.code !== '') {
                            url += `quantity.code=${filterBy.value.quantity.code}&`
                        }
                    }
                    url += `page=${this.currentPage}`
                    response = await api(url, 'GET')
                } else {
                    let url = '/api/'
                    if (filterBy.value.component !== '') {
                        url += `component-stocks?warehouse=${this.warehouseID}&order%5B${filterBy.value.component}%5D=${payload.trier.value}&item=${filterBy.value.component}&`
                    }
                    if (filterBy.value.product !== '') {
                        url += `product-stocks?warehouse=${this.warehouseID}&order%5B${filterBy.value.product}%5D=${payload.trier.value}&item=${filterBy.value.product}&`
                    }
                    if (filterBy.value.component === '' && filterBy.value.product === ''){
                        url += `stocks?warehouse=${this.warehouseID}&order%5B${filterBy.value.component}%5D=${payload.trier.value}&`
                    }
                    if (filterBy.value.batchNumber !== '') {
                        url += `batchNumber=${filterBy.value.batchNumber}&`
                    }
                    if (filterBy.value.location !== '') {
                        url += `location=${filterBy.value.location}&`
                    }
                    if (filterBy.value.jail !== '') {
                        if (filterBy.value.jail === true){
                            url += 'jail=1&'
                        } else {
                            url += 'jail=0&'
                        }
                    }
                    if (filterBy.value.quantity !== ''){
                        if (filterBy.value.quantity.value !== '') {
                            url += `quantity.value=${filterBy.value.quantity.value}&`
                        }
                        if (filterBy.value.quantity.code !== '') {
                            url += `quantity.code=${filterBy.value.quantity.code}&`
                        }
                    }
                    url += `page=${this.currentPage}`
                    response = await api(url, 'GET')
                }
                this.warehousesStock = await this.updatePagination(response)
            } else {
                if (payload.component === 'component') {
                    response = await api(`/api/stocks?warehouse=${this.warehouseID}&order%5B${payload.component}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                } else {
                    response = await api(`/api/stocks?warehouse=${this.warehouseID}&order%5B${payload.product}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
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
                await this.paginationSortableOrFilterItems({filter: payload.filter, filterBy: payload.filterBy, nPage: this.currentPage, sortable: payload.sortable, trierAlpha: payload.trierAlpha})
            } else {
                await this.itemsPagination(this.currentPage)
            }
            await this.fetch()
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
                component: comp,
                product: prod,
                batchNumber: numSerie,
                location: loc,
                quantity: quant,
                jail: pris
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
