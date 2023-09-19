import api from '../../api'
import {defineStore} from 'pinia'

export const useCompanyListSupplyStore = defineStore('companyListSupply', {
    actions: {
        setIdCompany(id){
            this.companyID = id
        },
        async deleted(payload) {
            await api(`/api/supplies/${payload}`, 'DELETE')
            this.companySupplies = this.companySupplies.filter(retard => Number(retard['@id'].match(/\d+/)[0]) !== payload)
        },
        async fetch(criteria = '?page=1') {
            if (this.currentPage < 1){
                this.currentPage = 1
            }
            const response = await api(`/api/supplies${criteria}`, 'GET')
            this.companySupplies = await this.updatePagination(response)
        },
        async filterBy(payload) {
            let url = `/api/supplies?company=/api/companies/${this.companyID}&`
            if (payload === ''){
                await this.fetch()
            } else {
                if (payload.ref !== '') {
                    url += `ref=${payload.ref}&`
                }
                url += 'page=1'
                this.currentPage = 1
                const response = await api(url, 'GET')
                this.companySupplies = await this.updatePagination(response)
            }
        },
        async itemsPagination(nPage) {
            const response = await api(`/api/supplies?company=/api/companies/${this.companyID}&page=${nPage}`, 'GET')
            this.companySupplies = await this.updatePagination(response)
        },
        async paginationSortableOrFilterItems(payload) {
            let response = {}
            if (payload.filter.value === true && payload.sortable.value === true){
                let url = `/api/supplies?company=/api/companies/${this.companyID}&`
                if (payload.filterBy.value.ref !== '') {
                    url += `ref=${payload.filterBy.value.ref}&`
                }
                response = await api(url, 'GET')
                this.companySupplies = await this.updatePagination(response)
            } else if (payload.filter.value === true){
                let url = `/api/supplies?company=/api/companies/${this.companyID}&`
                if (payload.filterBy.value.ref !== '') {
                    url += `ref=${payload.filterBy.value.ref}&`
                }
                url += `page=${payload.nPage}`
                response = await api(url, 'GET')
                this.companySupplies = await this.updatePagination(response)
            } else if (payload.sortable.value === false) {
                response = await api(`/api/supplies?company=/api/companies/${this.companyID}&page=${payload.nPage}`, 'GET')
                this.companySupplies = await this.updatePagination(response)
            } else {
                if (payload.trierAlpha.value.composant === 'company') {
                    response = await api(`/api/supplies?company=/api/companies/${this.companyID}&order%5B${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                } else {
                    response = await api(`/api/supplies?company=/api/companies/${this.companyID}&order%5Baddress.${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                }
                this.companySupplies = await this.updatePagination(response)
            }
        },

        async sortableItems(payload, filterBy, filter) {
            let response = {}
            if (filter.value === true){
                if (filterBy.value.ref !== '') {
                    url += `ref=${filterBy.value.ref}&`
                }
                url += `page=${this.currentPage}`
                response = await api(url, 'GET')
                this.companySupplies = await this.updatePagination(response)
            } else {
                if (payload.composant === 'company') {
                    response = await api(`/api/supplies?company=/api/companies/${this.companyID}&order%5B${payload.composant}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                } else {
                    response = await api(`/api/supplies?company=/api/companies/${this.companyID}&order%5B${payload.produit}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                }
                this.companySupplies = await this.updatePagination(response)
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
        }
    },
    getters: {
        itemsCompanySupply: state => state.companySupplies.map(item => {
            const newObject = {
                '@id': `${item['@id']}`,
                ref: item.ref,
                proportion: item.proportion,
                'product.code': item.product.code,
                'product.index': item.product.index,
                'product.name': item.product.name,
                'product.kind': item.product.kind,
                'product.@id': item.product['@id']
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
        companySupplies: [],
        companyID: 0
    })
})
