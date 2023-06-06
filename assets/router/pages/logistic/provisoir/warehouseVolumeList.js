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
        },
        async filterBy(payload) {
            let url = '/api/'
            if (payload.ref === '' && payload.quantite === '' && payload.type === ''){
                await this.fetch()
                return
            }

            if (payload.ref !== '' && payload.type !== '' && (payload.ref.includes('components') && payload.type === 'Produit' || payload.ref.includes('products') && payload.type === 'Composant')){
                this.warehousesVolume = []
                return
            }
            if (payload.ref === '' && payload.type === ''){
                url += `stocks?warehouse=${this.warehouseID}&item=${payload.ref}&`
            }

            if (payload.ref !== '') {
                if (payload.ref.includes('components')){
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
            this.warehousesVolume = await this.updatePagination(response)
        },
        async itemsPagination(nPage) {
            const response = await api(`/api/stocks?warehouse=${this.warehouseID}&page=${nPage}`, 'GET')
            this.warehousesVolume = await this.updatePagination(response)
        },
        async paginationSortableOrFilterItems(payload) {
            let response = {}
            if (payload.filter.value === true && payload.sortable.value === true){
                if (payload.trierAlpha.value.ref === 'ref') {
                    let url = '/api/'

                    if (payload.filterBy.value.ref === '' && payload.filterBy.value.type === ''){
                        url += `stocks?warehouse=${this.warehouseID}&item=${payload.filterBy.value.ref}&order%5B${payload.trierAlpha.value.ref}%5D=${payload.trierAlpha.value.trier.value}&`
                    }
                    if (payload.filterBy.value.ref !== '') {
                        if (payload.filterBy.value.ref.includes('components')){
                            url += `component-stocks?warehouse=${this.warehouseID}&item=${payload.filterBy.value.ref}&order%5B${payload.trierAlpha.value.ref}%5D=${payload.trierAlpha.value.trier.value}&`
                        } else {
                            url += `product-stocks?warehouse=${this.warehouseID}&item=${payload.filterBy.value.ref}&order%5B${payload.trierAlpha.value.ref}%5D=${payload.trierAlpha.value.trier.value}&`
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
                    if (payload.filterBy.value.type !== '') {
                        if (payload.filterBy.value.type === 'Composant'){
                            url += `component-stocks?warehouse=${this.warehouseID}&order%5B${payload.trierAlpha.value.ref}%5D=${payload.trierAlpha.value.trier.value}&`
                        } else {
                            url += `product-stocks?warehouse=${this.warehouseID}&order%5B${payload.trierAlpha.value.ref}%5D=${payload.trierAlpha.value.trier.value}&`
                        }
                    }
                    url += `page=${payload.nPage}`
                    response = await api(url, 'GET')
                    this.warehousesVolume = await this.updatePagination(response)
                } else {
                    let url = '/api/'
                    if (payload.filterBy.value.ref !== '') {
                        if (payload.filterBy.value.ref.includes('components')){
                            url += `component-stocks?warehouse=${this.warehouseID}&item=${payload.filterBy.value.ref}&order%5B${payload.trierAlpha.value.ref}%5D=${payload.trierAlpha.value.trier.value}&`
                        } else {
                            url += `product-stocks?warehouse=${this.warehouseID}&item=${payload.filterBy.value.ref}&order%5B${payload.trierAlpha.value.ref}%5D=${payload.trierAlpha.value.trier.value}&`
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
                    if (payload.filterBy.value.type !== '') {
                        if (payload.filterBy.value.type === 'Composant'){
                            url += `component-stocks?warehouse=${this.warehouseID}&order%5B${payload.trierAlpha.value.ref}%5D=${payload.trierAlpha.value.trier.value}&`
                        } else {
                            url += `product-stocks?warehouse=${this.warehouseID}&order%5B${payload.trierAlpha.value.ref}%5D=${payload.trierAlpha.value.trier.value}&`
                        }
                    }
                    url += `page=${payload.nPage}`
                    response = await api(url, 'GET')
                    this.warehousesVolume = await this.updatePagination(response)
                }
            } else if (payload.filter.value === true){
                let url = '/api/'
                if (payload.filterBy.value.ref === '' && payload.filterBy.value.type === ''){
                    url += `stocks?warehouse=${this.warehouseID}&item=${payload.filterBy.value.ref}&`
                }

                if (payload.filterBy.value.ref !== '') {
                    if (payload.filterBy.value.ref.includes('components')){
                        url += `component-stocks?warehouse=${this.warehouseID}&item=${payload.filterBy.value.ref}&`
                    } else {
                        url += `product-stocks?warehouse=${this.warehouseID}&item=${payload.filterBy.value.ref}&`
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
                if (payload.filterBy.value.type !== '') {
                    if (payload.filterBy.value.type === 'Composant'){
                        url += `component-stocks?warehouse=${this.warehouseID}&`
                    } else {
                        url += `product-stocks?warehouse=${this.warehouseID}&`
                    }
                }
                url += `page=${payload.nPage}`
                response = await api(url, 'GET')
                this.warehousesVolume = await this.updatePagination(response)
            } else if (payload.sortable.value === false) {
                response = await api(`/api/stocks?warehouse=${this.warehouseID}&page=${payload.nPage}`, 'GET')
                this.warehousesVolume = await this.updatePagination(response)
            } else {
                if (payload.trierAlpha.value.ref === 'ref') {
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
                if (payload.ref === 'ref') {
                    let url = '/api/'
                    if (filterBy.value.ref === '' && filterBy.value.type === ''){
                        url += `stocks?warehouse=${this.warehouseID}&item=${filterBy.value.ref}&order%5B${payload.ref}%5D=${payload.trier.value}&`
                    }
                    if (filterBy.value.ref !== '') {
                        if (filterBy.value.ref.includes('components')){
                            url += `component-stocks?warehouse=${this.warehouseID}&item=${filterBy.value.ref}&order%5B${payload.ref}%5D=${payload.trier.value}&`
                        } else {
                            url += `product-stocks?warehouse=${this.warehouseID}&item=${filterBy.value.ref}&order%5B${payload.ref}%5D=${payload.trier.value}&`
                        }
                    }
                    if (filterBy.value.type !== '') {
                        if (filterBy.value.type === 'Composant'){
                            url += `component-stocks?warehouse=${this.warehouseID}&item=${filterBy.value.ref}&order%5B${payload.ref}%5D=${payload.trier.value}&`
                        } else {
                            url += `product-stocks?warehouse=${this.warehouseID}&item=${filterBy.value.ref}&order%5B${payload.ref}%5D=${payload.trier.value}&`
                        }
                    }
                    url += `page=${this.currentPage}`
                    response = await api(url, 'GET')
                } else {
                    let url = '/api/'

                    if (filterBy.value.ref === '' && filterBy.value.type === ''){
                        url += `stocks?warehouse=${this.warehouseID}&item=${filterBy.value.ref}&`
                    }
                    if (filterBy.value.ref !== '') {
                        if (filterBy.value.ref.includes('components')){
                            url += `component-stocks?warehouse=${this.warehouseID}&item=${filterBy.value.ref}&`
                        } else {
                            url += `product-stocks?warehouse=${this.warehouseID}&item=${filterBy.value.ref}&`
                        }
                    }
                    if (filterBy.value.type !== '') {
                        if (filterBy.value.type === 'Composant'){
                            url += `component-stocks?warehouse=${this.warehouseID}&`
                        } else {
                            url += `product-stocks?warehouse=${this.warehouseID}&`
                        }
                        url += `page=${this.currentPage}`
                        response = await api(url, 'GET')
                    }
                    this.warehousesVolume = await this.updatePagination(response)
                    url += `page=${this.currentPage}`
                    response = await api(url, 'GET')
                }
                this.warehousesVolume = await this.updatePagination(response)
            } else {
                if (payload.ref === 'ref') {
                    response = await api(`/api/stocks?warehouse=${this.warehouseID}&order%5B${payload.ref}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                } else {
                    response = await api(`/api/stocks?warehouse=${this.warehouseID}&order%5B${payload.ref}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
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
            this.fetch()
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
        async getOptionRef() {
            const opt = []
            const codes = new Set()
            const response = await api(`/api/stocks?warehouse=${this.warehouseID}&pagination=false`, 'GET')

            for (const warehouse of response['hydra:member']) {
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
        getOptionType() {
            const opt = []
            opt.push({text: 'Composant'})
            opt.push({text: 'Produit'})
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
