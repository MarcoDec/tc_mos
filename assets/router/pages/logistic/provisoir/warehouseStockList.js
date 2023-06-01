import api from '../../../../api'
import {defineStore} from 'pinia'

export const useWarehouseStockListStore = defineStore('warehouseStockList', {
    actions: {
        setIdWarehouse(id){
            this.warehouseID = id
        },
        async addWarehouseStock(payload){
            console.log(payload)
            if (!payload.composant || !payload.produit) {
                if (payload.prison === ''){
                    payload.prison = false
                }
                if (payload.composant) {
                    // const element = [
                    //     {
                    //         '@context': '/api/contexts/ComponentStock',
                    //         '@id': payload.composant,
                    //         '@type': 'ComponentStock',
                    //         batchNumber: payload.numeroDeSerie,
                    //         item: payload.composant,
                    //         jail: payload.prison,
                    //         location: payload.localisation,
                    //         quantity: {
                    //             code: 'U',
                    //             value: 1
                    //         }
                    //     },
                    //     {warehouse: `/api/warehouses/${this.warehouseID}`}
                    // ]
                    const element = {
                        batchNumber: '1654854543',
                        item: '/api/components/1',
                        jail: true,
                        location: 'Rayon B',
                        quantity: {
                            code: 'm',
                            value: 1
                        },
                        warehouse: '/api/warehouses/1'
                    }
                    console.log(element)
                    await api('/api/component-stocks', 'POST', element)
                } else {
                    const element = [
                        {
                            item: '/api/products/faire',
                            batchNumber: payload.numeroDeSerie,
                            jail: payload.prison,
                            location: payload.localisation,
                            quantity: payload.quantite
                        },
                        {
                            warehouse: payload.warehouse
                        }
                    ]
                    await api('/api/product-stocks', 'POST', element)
                }
            }
            this.fetch()
        },
        async deleted(payload) {
            await api(`/api/stocks/${payload}`, 'DELETE')
            this.warehousesStock = this.warehousesStock.filter(warehouse => Number(warehouse['@id'].match(/\d+/)[0]) !== payload)
        },
        async fetch() {
            const response = await api(`/api/stocks?warehouse=${this.warehouseID}`, 'GET')
            this.warehousesStock = await this.updatePagination(response)
            // this.warehousesStock.sort((a, b) => a.batchNumber.localeCompare(b.batchNumber))
        },
        async filterBy(payload) {
            let url = '/api/'
            if (payload.composant === '' && payload.produit === '' && payload.numeroDeSerie === '' && payload.localisation === ''){
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
                    url += `jail=${payload.prison}&`
                }
                // if (payload.quantite !== '') {
                //      url += `quantite=${payload.quantite}&`
                // }
                url += 'page=1'
                const response = await api(url, 'GET')
                this.warehousesStock = await this.updatePagination(response)
            }
        },
        async itemsPagination(nPage) {
            const response = await api(`/api/stocks?page=${nPage}`, 'GET')
            this.warehousesStock = await this.updatePagination(response)
        },
        async paginationSortableOrFilterItems(payload) {
            let response = {}
            if (payload.filter.value === true && payload.sortable.value === true){
                if (payload.trierAlpha.value.component === 'component') {
                    let url = `/api/stocks?warehouse=${this.warehouseID}&order%5B${payload.trierAlpha.value.component}%5D=${payload.trierAlpha.value.trier.value}&`
                    if (payload.filterBy.value.component !== '') {
                        url += `component=${payload.filterBy.value.component}&`
                    }
                    if (payload.produit !== '') {
                        url += `product=${payload.produit}&`
                    }
                    if (payload.numeroDeSerie !== '') {
                        url += `batchNumber=${payload.numeroDeSerie}&`
                    }
                    if (payload.localisation !== '') {
                        url += `location=${payload.localisation}&`
                    }
                    // if (payload.quantite !== '') {
                    //     url += `quantite=${payload.quantite}&`
                    // }
                    url += `page=${payload.nPage}`
                    response = await api(url, 'GET')
                    this.societies = await this.updatePagination(response)
                } else {
                    let url = `/api/stocks?warehouse=${this.warehouseID}&order%5Baddress.${payload.trierAlpha.value.component}%5D=${payload.trierAlpha.value.trier.value}&`
                    if (payload.filterBy.value.component !== '') {
                        url += `component=${payload.filterBy.value.component}&`
                    }
                    if (payload.produit !== '') {
                        url += `product=${payload.produit}&`
                    }
                    if (payload.numeroDeSerie !== '') {
                        url += `batchNumber=${payload.numeroDeSerie}&`
                    }
                    if (payload.localisation !== '') {
                        url += `location=${payload.localisation}&`
                    }
                    // if (payload.quantite !== '') {
                    //     url += `quantite=${payload.quantite}&`
                    // }
                    // url += `page=${payload.nPage}`
                    response = await api(url, 'GET')
                    this.warehousesStock = await this.updatePagination(response)
                }
            } else if (payload.filter.value === true){
                let url = `/api/stocks?warehouse=${this.warehouseID}&`
                if (payload.filterBy.value.component !== '') {
                    url += `component=${payload.filterBy.value.component}&`
                }
                if (payload.produit !== '') {
                    url += `product=${payload.produit}&`
                }
                if (payload.numeroDeSerie !== '') {
                    url += `batchNumber=${payload.numeroDeSerie}&`
                }
                if (payload.localisation !== '') {
                    url += `location=${payload.localisation}&`
                }
                // if (payload.quantite !== '') {
                //     url += `quantite=${payload.quantite}&`
                // }
                url += `page=${payload.nPage}`
                response = await api(url, 'GET')
                this.warehousesStock = await this.updatePagination(response)
            } else if (payload.sortable.value === false) {
                response = await api(`/api/stocks?page=${payload.nPage}`, 'GET')
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

        async sortableItems(payload, filterBy, filter) {
            let response = {}
            if (filter.value === true){
                if (payload.component === 'component') {
                    let url = `/api/stocks?warehouse=${this.warehouseID}&order%5B${payload.component}%5D=${payload.trier.value}&`
                    if (filterBy.value.component !== '') {
                        url += `component=${filterBy.value.component}&`
                    }
                    if (payload.produit !== '') {
                        url += `product=${payload.produit}&`
                    }
                    if (payload.numeroDeSerie !== '') {
                        url += `batchNumber=${payload.numeroDeSerie}&`
                    }
                    if (payload.localisation !== '') {
                        url += `location=${payload.localisation}&`
                    }
                    // if (payload.quantite !== '') {
                    //     url += `quantite=${payload.quantite}&`
                    // }
                    url += `page=${this.currentPage}`
                    response = await api(url, 'GET')
                } else {
                    let url = `/api/stocks?warehouse=${this.warehouseID}&order%5B${payload.component}%5D=${payload.trier.value}&`
                    if (filterBy.value.component !== '') {
                        url += `component=${filterBy.value.component}&`
                    }
                    if (payload.produit !== '') {
                        url += `product=${payload.produit}&`
                    }
                    if (payload.numeroDeSerie !== '') {
                        url += `batchNumber=${payload.numeroDeSerie}&`
                    }
                    if (payload.localisation !== '') {
                        url += `location=${payload.localisation}&`
                    }
                    // if (payload.quantite !== '') {
                    //     url += `quantite=${payload.quantite}&`
                    // }
                    url += `page=${this.currentPage}`
                    response = await api(url, 'GET')
                }
                this.warehousesStock = await this.updatePagination(response)
            } else {
                if (payload.component === 'component') {
                    response = await api(`/api/stocks?warehouse=${this.warehouseID}&order%5B${payload.component}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                } else {
                    response = await api(`/api/stocks?warehouse=${this.warehouseID}&order%5B${payload.component}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
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
                    opt.push({value: warehouse.item['@id'], '@type': warehouse.item['@type'], text: warehouse.item.code, id: warehouse.item.id})
                    codes.add(warehouse.item.code)
                }
            }
            //opt.sort((a, b) => a.text.localeCompare(b.text))
            return opt.length === 0 ? [{text: 'Aucun élément'}] : opt
        },
        getOptionProduit() {
            const opt = []
            const codes = new Set()

            for (const warehouse of this.warehousesStock) {
                if (warehouse['@type'] === 'ProductStock' && !codes.has(warehouse.item.code)) {
                    opt.push({value: warehouse.item['@id'], '@type': warehouse.item['@type'], text: warehouse.item.code, id: warehouse.item.id})
                    codes.add(warehouse.item.code)
                }
            }
            //opt.sort((a, b) => a.text.localeCompare(b.text))
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
