import api from '../../../../api'
import {defineStore} from 'pinia'

export const useWarehouseVolumeListStore = defineStore('warehouseVolumeList', {
    actions: {
        setIdWarehouse(id){
            this.warehouseID = id
        },
        async addWarehouseVolume(payload){
            await api('/api/stocks', 'POST', payload)
            this.fetch()
        },
        async deleted(payload) {
            await api(`/api/stocks/${payload}`, 'DELETE')
            this.warehousesVolume = this.warehousesVolume.filter(warehouse => Number(warehouse['@id'].match(/\d+/)[0]) !== payload)
        },
        async fetch() {
            const response = await api(`/api/stocks?warehouse=${this.warehouseID}`, 'GET')
            this.warehousesVolume = await this.updatePagination(response)
            // this.warehousesVolume.sort((a, b) => a.batchNumber.localeCompare(b.batchNumber))
        },
        async filterBy(payload) {
            let url = '/api/'
            if (payload.ref === '' && payload.type === ''){
                await this.fetch()
                return
            }

            if (payload.ref !== '' && payload.type !== '' && (payload.ref['@type'] === 'Component' && payload.type === 'Produit' || payload.ref['@type'] === 'Product' && payload.type === 'Composant')){
                this.warehousesVolume = []
                return
            }

            if (payload.ref !== '') {
                if (payload.ref['@type'] === 'Component'){
                    url += `component-stocks?warehouse=${this.warehouseID}&item=${payload.ref}&`
                } else {
                    url += `product-stocks?warehouse=${this.warehouseID}&item=${payload.ref}&`
                }
            }
            if (payload.type !== '') {
                if (payload.type === 'Composant'){
                    url += `component-stocks?warehouse=${this.warehouseID}&`
                } else {
                    url += `product-stocks?warehouse=${this.warehouseID}&`
                }
            }
            // if (payload.quantite !== '') {
            //      url += `quantite=${payload.quantite}&`
            // }
            url += 'page=1'
            const response = await api(url, 'GET')
            this.warehousesVolume = await this.updatePagination(response)
        },
        async itemsPagination(nPage) {
            const response = await api(`/api/stocks?page=${nPage}`, 'GET')
            this.warehousesVolume = await this.updatePagination(response)
        },
        async paginationSortableOrFilterItems(payload) {
            let response = {}
            if (payload.filter.value === true && payload.sortable.value === true){
                if (payload.trierAlpha.value.ref === 'ref') {
                    let url = `/api/stocks?warehouse=${this.warehouseID}&order%5B${payload.trierAlpha.value.ref}%5D=${payload.trierAlpha.value.trier.value}&`
                    if (payload.filterBy.value.ref !== '') {
                        url += `ref=${payload.filterBy.value.ref}&`
                    }
                    // if (payload.qualite !== '') {
                    //     url += `item=${payload.qualite}&`
                    // }
                    if (payload.type !== '') {
                        url += `type=${payload.type}&`
                    }
                    url += `page=${payload.nPage}`
                    response = await api(url, 'GET')
                    this.societies = await this.updatePagination(response)
                } else {
                    let url = `/api/stocks?warehouse=${this.warehouseID}&order%5Baddress.${payload.trierAlpha.value.ref}%5D=${payload.trierAlpha.value.trier.value}&`
                    if (payload.filterBy.value.ref !== '') {
                        url += `ref=${payload.filterBy.value.ref}&`
                    }
                    // if (payload.qualite !== '') {
                    //     url += `item=${payload.qualite}&`
                    // }
                    if (payload.type !== '') {
                        url += `type=${payload.type}&`
                    }
                    url += `page=${payload.nPage}`
                    response = await api(url, 'GET')
                    this.warehousesVolume = await this.updatePagination(response)
                }
            } else if (payload.filter.value === true){
                let url = `/api/stocks?warehouse=${this.warehouseID}&`
                if (payload.filterBy.value.ref !== '') {
                    url += `ref=${payload.filterBy.value.ref}&`
                }
                // if (payload.qualite !== '') {
                //     url += `item=${payload.qualite}&`
                // }
                if (payload.type !== '') {
                    url += `type=${payload.type}&`
                }
                url += `page=${payload.nPage}`
                response = await api(url, 'GET')
                this.warehousesVolume = await this.updatePagination(response)
            } else if (payload.sortable.value === false) {
                response = await api(`/api/stocks?page=${payload.nPage}`, 'GET')
                this.warehousesVolume = await this.updatePagination(response)
            } else {
                if (payload.trierAlpha.value.component === 'component') {
                    response = await api(`/api/stocks?warehouse=${this.warehouseID}&order%5B${payload.trierAlpha.value.ref}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                } else {
                    response = await api(`/api/stocks?warehouse=${this.warehouseID}&order%5Baddress.${payload.trierAlpha.value.ref}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                }
                this.warehousesVolume = await this.updatePagination(response)
            }
        },

        async sortableItems(payload, filterBy, filter) {
            let response = {}
            if (filter.value === true){
                if (payload.component === 'component') {
                    let url = `/api/stocks?warehouse=${this.warehouseID}&order%5B${payload.ref}%5D=${payload.trier.value}&`
                    if (payload.filterBy.value.ref !== '') {
                        url += `ref=${payload.filterBy.value.ref}&`
                    }
                    // if (payload.qualite !== '') {
                    //     url += `item=${payload.qualite}&`
                    // }
                    if (payload.type !== '') {
                        url += `type=${payload.type}&`
                    }
                    url += `page=${this.currentPage}`
                    response = await api(url, 'GET')
                } else {
                    let url = `/api/stocks?warehouse=${this.warehouseID}&order%5B${payload.ref}%5D=${payload.trier.value}&`
                    if (payload.filterBy.value.ref !== '') {
                        url += `ref=${payload.filterBy.value.ref}&`
                    }
                    // if (payload.qualite !== '') {
                    //     url += `item=${payload.qualite}&`
                    // }
                    if (payload.type !== '') {
                        url += `type=${payload.type}&`
                    }
                    url += `page=${this.currentPage}`
                    response = await api(url, 'GET')
                }
                this.warehousesVolume = await this.updatePagination(response)
            } else {
                if (payload.component === 'component') {
                    response = await api(`/api/stocks?warehouse=${this.warehouseID}&order%5B${payload.component}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                } else {
                    response = await api(`/api/stocks?warehouse=${this.warehouseID}&order%5B${payload.component}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                }
                this.warehousesVolume = await this.updatePagination(response)
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
        async updateWarehouseVolume(payload){
            await api(`/api/stocks/${payload.id}`, 'PATCH', payload.itemsUpdateData)
            if (payload.sortable.value === true || payload.filter.value === true) {
                this.paginationSortableOrFilterItems({filter: payload.filter, filterBy: payload.filterBy, nPage: this.currentPage, sortable: payload.sortable, trierAlpha: payload.trierAlpha})
            } else {
                this.itemsPagination(this.currentPage)
            }
        }

    },
    getters: {
        itemsWarehousesVolume: state => state.warehousesVolume.map(item => {
            const reference = item.item.code
            const quant = item.quantity
            let typ = 'Composant'
            if (item['@type'] === 'ProductStock'){
                typ = 'Produit'
            }
            const newObject = {
                ...item,
                ref: reference,
                quantite: quant,
                type: typ
            }
            return newObject
        }),
        getOptionRef() {
            const opt = []
            const codes = new Set()
            for (const warehouse of this.warehousesVolume) {
                if (!codes.has(warehouse.item.code)) {
                    if (warehouse['@type'] === 'ProductStock') {
                        opt.push({value: warehouse.item['@id'], text: warehouse.item.code})
                        codes.add(warehouse.item.code)
                    } else {
                        opt.push({value: warehouse.item['@id'], text: warehouse.item.code})
                        codes.add(warehouse.item.code)
                    }
                }
            }
            return opt.length === 0 ? [{text: 'Aucun élément'}] : opt
        },
        getOptionType() {
            const opt = []
            const codes = new Set()
            for (const warehouse of this.warehousesVolume) {
                if (!codes.has(warehouse['@type'])) {
                    if (warehouse['@type'] === 'ComponentStock') {
                        opt.push({text: 'Composant'})
                        codes.add(warehouse['@type'])
                    } else {
                        opt.push({text: 'Produit'})
                        codes.add(warehouse['@type'])
                    }
                }
            }

            opt.sort((a, b) => a.text.localeCompare(b.text))
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
        warehousesVolume: [],
        warehouseID: 0
    })
})
