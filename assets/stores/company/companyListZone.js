import api from '../../api'
import {defineStore} from 'pinia'

export const useCompanyListZoneStore = defineStore('companyListZone', {
    actions: {
        setIdCompany(id){
            this.companyID = id
        },
        // async addZoneZone(payload){
        //     const violations = []
        //     try {
        //         if (payload.quantite.value !== ''){
        //             payload.quantite.value = parseInt(payload.quantite.value)
        //         }
        //         const element = {
        //             company: payload.composant,
        //             refFournisseur: payload.refFournisseur,
        //             prix: payload.prix,
        //             quantity: payload.quantite,
        //             texte: payload.texte
        //         }
        //         await api('/api/company-stocks', 'POST', element)
        //         this.fetch()
        //     } catch (error) {
        //         violations.push({message: error})
        //     }
        //     return violations
        // },
        async deleted(payload) {
            await api(`/api/zones/${payload}`, 'DELETE')
            this.zones = this.zones.filter(retard => Number(retard['@id'].match(/\d+/)[0]) !== payload)
        },
        async fetch(criteria = '?page=1') {
            if (this.currentPage < 1){
                this.currentPage = 1
            }
            const response = await api(`/api/zones${criteria}`, 'GET')
            this.zones = await this.updatePagination(response)
        },
        async filterBy(payload) {
            let url = `/api/zones?company=/api/companies/${this.companyID}&`
            if (payload === ''){
                await this.fetch()
            } else {
                if (payload.name !== '') {
                    url += `name=${payload.name}&`
                }
                url += 'page=1'
                this.currentPage = 1
                const response = await api(url, 'GET')
                this.zones = await this.updatePagination(response)
            }
        },
        async itemsPagination(nPage) {
            const response = await api(`/api/zones?company=/api/companies/${this.companyID}&page=${nPage}`, 'GET')
            this.zones = await this.updatePagination(response)
        },
        async paginationSortableOrFilterItems(payload) {
            let response = {}
            if (payload.filter.value === true && payload.sortable.value === true){
                let url = `/api/zones?company=/api/companies/${this.companyID}&`
                if (payload.filterBy.value.name !== '') {
                    url += `name=${payload.filterBy.value.name}&`
                }
                response = await api(url, 'GET')
                this.zones = await this.updatePagination(response)
            } else if (payload.filter.value === true){
                let url = `/api/zones?company=/api/companies/${this.companyID}&`
                if (payload.filterBy.value.name !== '') {
                    url += `name=${payload.filterBy.value.name}&`
                }
                url += `page=${payload.nPage}`
                response = await api(url, 'GET')
                this.zones = await this.updatePagination(response)
            } else if (payload.sortable.value === false) {
                response = await api(`/api/zones?company=/api/companies/${this.companyID}&page=${payload.nPage}`, 'GET')
                this.zones = await this.updatePagination(response)
            } else {
                if (payload.trierAlpha.value.composant === 'company') {
                    response = await api(`/api/zones?company=/api/companies/${this.companyID}&order%5B${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                } else {
                    response = await api(`/api/zones?company=/api/companies/${this.companyID}&order%5Baddress.${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                }
                this.zones = await this.updatePagination(response)
            }
        },

        async sortableItems(payload, filterBy, filter) {
            let response = {}
            if (filter.value === true){
                if (filterBy.value.name !== '') {
                    url += `name=${filterBy.value.name}&`
                }
                url += `page=${this.currentPage}`
                response = await api(url, 'GET')
                this.zones = await this.updatePagination(response)
            } else {
                if (payload.composant === 'company') {
                    response = await api(`/api/zones?company=/api/companies/${this.companyID}&order%5B${payload.composant}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                } else {
                    response = await api(`/api/zones?company=/api/companies/${this.companyID}&order%5B${payload.produit}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                }
                this.zones = await this.updatePagination(response)
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
        itemsCompanyZone: state => state.zones.map(item => {
            const newObject = {
                '@id': `${item['@id']}`,
                name: item.name
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
        zones: [],
        companyID: 0
    })
})
