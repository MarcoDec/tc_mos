import api from '../../api'
import {defineStore} from 'pinia'

export const useCompanyListEventStore = defineStore('companyListEvent', {
    actions: {
        setIdCompany(id){
            this.companyID = id
        },
        // async addEventEvent(payload){
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
            await api(`/api/company-events/${payload}`, 'DELETE')
            this.companyEvent = this.companyEvent.filter(retard => Number(retard['@id'].match(/\d+/)[0]) !== payload)
        },
        async fetch() {
            if (this.currentPage < 1){
                this.currentPage = 1
            }
            const response = await api(`/api/company-events?company=/api/companies/${this.companyID}`, 'GET')
            this.companyEvent = await this.upentryDatePagination(response)
        },
        async filterBy(payload) {
            let url = `/api/company-events?company=/api/companies/${this.companyID}&`
            if (payload === ''){
                await this.fetch()
            } else {
                if (payload.name) {
                    url += `name=${payload.name}&`
                }
                if (payload.date) {
                    const payloadDate = new Date(payload.date)
                    const dateAfter = new Date()
                    dateAfter.setDate(payloadDate.getDate() + 1)
                    const dateAfterStr = dateAfter.toISOString().split('T')[0]
                    url += `date[after]=${payload.date}&date[before]=${dateAfterStr}&`
                }
                if (payload.kind) {
                    url += `kind=${payload.type}&`
                }
                if (typeof payload.done !== 'undefined' && payload.done === true){
                    url += 'done=1&'
                }
                if (typeof payload.done !== 'undefined' && payload.done === false){
                    url += 'done=0&'
                }
                url += 'page=1'
                this.currentPage = 1
                const response = await api(url, 'GET')
                this.companyEvent = await this.upentryDatePagination(response)
            }
        },
        async itemsPagination(nPage) {
            const response = await api(`/api/company-events?company=/api/companies/${this.companyID}&page=${nPage}`, 'GET')
            this.companyEvent = await this.upentryDatePagination(response)
        },
        async paginationSortableOrFilterItems(payload) {
            let response = {}
            if (payload.filter.value === true && payload.sortable.value === true){
                let url = `/api/company-events?company=/api/companies/${this.companyID}&`
                if (payload.filterBy.value.name !== '') {
                    url += `name=${payload.filterBy.value.name}&`
                }
                if (payload.filterBy.value.date !== '') {
                    url += `date=${payload.filterBy.value.date}&`
                }
                if (payload.filterBy.value.type !== '') {
                    url += `kind=${payload.filterBy.value.type}&`
                }
                if (payload.filterBy.value.done !== '') {
                    if (payload.filterBy.value.done){
                        url += 'done=1&'
                    } else {
                        url += 'done=0&'
                    }
                }
                response = await api(url, 'GET')
                this.companyEvent = await this.upentryDatePagination(response)
            } else if (payload.filter.value === true){
                let url = `/api/company-events?company=/api/companies/${this.companyID}&`
                if (payload.filterBy.value.name !== '') {
                    url += `name=${payload.filterBy.value.name}&`
                }
                if (payload.filterBy.value.date !== '') {
                    url += `date=${payload.filterBy.value.date}&`
                }
                if (payload.filterBy.value.type !== '') {
                    url += `kind=${payload.filterBy.value.type}&`
                }
                if (payload.filterBy.value.done !== '') {
                    if (payload.filterBy.value.done){
                        url += 'done=1&'
                    } else {
                        url += 'done=0&'
                    }
                }
                url += `page=${payload.nPage}`
                response = await api(url, 'GET')
                this.companyEvent = await this.upentryDatePagination(response)
            } else if (payload.sortable.value === false) {
                response = await api(`/api/company-events?company=/api/companies/${this.companyID}&page=${payload.nPage}`, 'GET')
                this.companyEvent = await this.upentryDatePagination(response)
            } else {
                if (payload.trierAlpha.value.composant === 'company') {
                    response = await api(`/api/company-events?company=/api/companies/${this.companyID}&order%5B${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                } else {
                    response = await api(`/api/company-events?company=/api/companies/${this.companyID}&order%5Baddress.${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                }
                this.companyEvent = await this.upentryDatePagination(response)
            }
        },

        async sortableItems(payload, filterBy, filter) {
            let response = {}
            let url = ''
            if (filter.value === true){
                if (filterBy.value.name !== '') {
                    url += `name=${filterBy.value.name}&`
                }
                if (filterBy.value.date !== '') {
                    url += `date=${filterBy.value.date}&`
                }
                if (filterBy.value.type !== '') {
                    url += `kind=${filterBy.value.type}&`
                }
                if (filterBy.value.done !== '') {
                    if (filterBy.value.done){
                        url += 'done=1&'
                    } else {
                        url += 'done=0&'
                    }
                }
                url += `page=${this.currentPage}`
                response = await api(url, 'GET')
                this.companyEvent = await this.upentryDatePagination(response)
            } else {
                if (payload.composant === 'company') {
                    response = await api(`/api/company-events?company=/api/companies/${this.companyID}&order%5B${payload.composant}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                } else {
                    response = await api(`/api/company-events?company=/api/companies/${this.companyID}&order%5B${payload.produit}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                }
                this.companyEvent = await this.upentryDatePagination(response)
            }
        },
        async upentryDatePagination(response) {
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
        // async upentryDateWarehouseStock(payload){
        //     await api(`/api/stocks/${payload.id}`, 'PATCH', payload.itemsUpentryDateData)
        //     if (payload.sortable.value === true || payload.filter.value === true) {
        //         this.paginationSortableOrFilterItems({filter: payload.filter, filterBy: payload.filterBy, nPage: this.currentPage, sortable: payload.sortable, trierAlpha: payload.trierAlpha})
        //     } else {
        //         this.itemsPagination(this.currentPage)
        //     }
        //     this.fetch()
        // }
    },
    getters: {
        itemsCompanyEvent: state => state.companyEvent.map(item => {
            const dt = item.date.split('T')[0]
            const newObject = {
                '@id': `${item['@id']}`,
                name: item.name,
                kind: item.kind,
                date: dt,
                done: item.done
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
        companyEvent: [],
        companyID: 0
    })
})
