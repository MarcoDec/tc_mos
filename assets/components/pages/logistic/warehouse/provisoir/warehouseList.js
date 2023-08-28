import api from '../../../../../api'
import {defineStore} from 'pinia'

export const useWarehouseListStore = defineStore('warehouseList', {
    actions: {
        async addWarehouse(payload){
            const violations = []
            try {
                await api('/api/warehouses', 'POST', payload)
            } catch (error){
                violations.push({message: 'Vous devez obligatoirement saisir un Nom'})
            }
            this.fetch()
            return violations
        },

        async delated(payload){
            await api(`/api/warehouses/${payload}`, 'DELETE')
            this.warehouses = this.warehouses.filter(warehouse => Number(warehouse['@id'].match(/\d+/)[0]) !== payload)
        },
        async fetch() {
            const response = await api('/api/warehouses', 'GET')
            this.warehouses = await this.updatePagination(response)
        },
        async filterBy(payload){
            let url = '/api/warehouses?'
            if (payload.name !== '') {
                url += `name=${payload.name}&`
            }
            if (payload.families !== '') {
                url += `families%5B%5D=${payload.families}&`
            }
            url += 'page=1'
            const response = await api(url, 'GET')
            this.warehouses = await this.updatePagination(response)
        },
        async itemsPagination(nPage) {
            const response = await api(`/api/warehouses?page=${nPage}`, 'GET')
            this.warehouses = await this.updatePagination(response)
        },
        async paginationSortableOrFilterItems(payload) {
            let response = {}
            if (payload.filter.value === true && payload.sortable.value === true){
                console.log('je suis ici')
                if (payload.trierAlpha.value.name === 'name') {
                    let url = `/api/warehouses?order%5B${payload.trierAlpha.value.name}%5D=${payload.trierAlpha.value.trier.value}&`
                    if (payload.filterBy.value.name !== '') {
                        url += `name=${payload.filterBy.value.name}&`
                    }
                    if (payload.filterBy.value.families !== '') {
                        url += `families%5B%5D=${payload.filterBy.value.families}&`
                    }
                    url += `page=${payload.nPage}`
                    response = await api(url, 'GET')
                    this.societies = await this.updatePagination(response)
                } else {
                    let url = `/api/warehouses?order%5B${payload.trierAlpha.value.name}%5D=${payload.trierAlpha.value.trier.value}&`
                    if (payload.filterBy.value.name !== '') {
                        url += `name=${payload.filterBy.value.name}&`
                    }
                    if (payload.filterBy.value.families !== '') {
                        url += `families%5B%5D=${payload.filterBy.value.families}&`
                    }
                    url += `page=${payload.nPage}`
                    response = await api(url, 'GET')
                    this.warehouses = await this.updatePagination(response)
                }
            } else if (payload.filter.value === true){
                console.log('je suis ici')
                let url = '/api/warehouses?'
                if (payload.filterBy.value.name !== '') {
                    url += `name=${payload.filterBy.value.name}&`
                }
                if (payload.filterBy.value.families !== '') {
                    url += `families%5B%5D==${payload.filterBy.value.families}&`
                }
                url += `page=${payload.nPage}`
                response = await api(url, 'GET')
                this.warehouses = await this.updatePagination(response)
            } else if (payload.sortable.value === false) {
                console.log('je suis ici')
                response = await api(`/api/warehouses?page=${payload.nPage}`, 'GET')
                console.log(response)
                this.warehouses = await this.updatePagination(response)
                console.log(this.warehouses)
            } else {
                if (payload.trierAlpha.value.name === 'name') {
                    response = await api(`/api/warehouses?order%5B${payload.trierAlpha.value.name}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                } else {
                    response = await api(`/api/warehouses?order%5B${payload.trierAlpha.value.name}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                }
                this.warehouses = await this.updatePagination(response)
            }
        },
        async sortableItems(payload, filterBy, filter) {
            let response = {}
            if (filter.value === true){
                if (payload.name === 'name') {
                    let url = `/api/warehouses?order%5B${payload.name}%5D=${payload.trier.value}&`
                    if (filterBy.value.name !== '') {
                        url += `name=${filterBy.value.name}&`
                    }
                    if (filterBy.value.families !== '') {
                        url += `families%5B%5D=${filterBy.value.families}&`
                    }
                    url += `page=${this.currentPage}`
                    response = await api(url, 'GET')
                } else {
                    let url = `/api/warehouses?order%5B${payload.name}%5D=${payload.trier.value}&`
                    if (filterBy.value.name !== '') {
                        url += `name=${filterBy.value.name}&`
                    }
                    if (filterBy.value.families !== '') {
                        url += `families%5B%5D=${filterBy.value.families}&`
                    }
                    url += `page=${this.currentPage}`
                    response = await api(url, 'GET')
                }
                this.warehouses = await this.updatePagination(response)
            } else {
                if (payload.name === 'name') {
                    response = await api(`/api/warehouses?order%5B${payload.name}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                } else {
                    response = await api(`/api/warehouses?order%5B${payload.name}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                }
                this.warehouses = await this.updatePagination(response)
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
        async updateWarehouse(payload){
            await api(`/api/warehouses/${payload.id}`, 'PATCH', payload.itemsUpdateData)
            if (payload.sortable.value === true || payload.filter.value === true) {
                this.paginationSortableOrFilterItems({filter: payload.filter, filterBy: payload.filterBy, nPage: this.currentPage, sortable: payload.sortable, trierAlpha: payload.trierAlpha})
            } else {
                this.itemsPagination(this.currentPage)
            }
        }

    },
    getters: {
        itemsWarehouses: state => state.warehouses.map(item => {
            const {families} = item
            const newObject = {
                ...item,
                families: families.toString() ?? null
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
        warehouses: [],
        families: []
    })
})
