import api from '../../api'
import {defineStore} from 'pinia'

export const useCompanyListEmployeStore = defineStore('companyListEmploye', {
    actions: {
        setIdCompany(id){
            this.companyID = id
        },
        async deleted(payload) {
            await api(`/api/employees/${payload}`, 'DELETE')
            this.employees = this.employees.filter(retard => Number(retard['@id'].match(/\d+/)[0]) !== payload)
        },
        async fetch(criteria = '?page=1') {
            if (this.currentPage < 1){
                this.currentPage = 1
            }
            const response = await api(`/api/employees${criteria}`, 'GET')
            this.employees = await this.updatePagination(response)
        },
        async filterBy(payload) {
            let url = `/api/employees?company=/api/companies/${this.companyID}&`
            if (payload === ''){
                await this.fetch()
            } else {
                if (payload.name !== '') {
                    url += `name=${payload.name}&`
                }
                if (payload.surname !== '') {
                    url += `surname=${payload.surname}&`
                }
                if (payload.notes !== '') {
                    url += `notes=${payload.notes}&`
                }
                if (payload.entryDate !== '') {
                    url += `entryDate=${payload.entryDate}&`
                }
                if (payload.initials !== '') {
                    url += `initials=${payload.initials}&`
                }
                if (payload.username !== '') {
                    url += `username=${payload.username}&`
                }
                url += 'page=1'
                this.currentPage = 1
                const response = await api(url, 'GET')
                this.employees = await this.updatePagination(response)
            }
        },
        async itemsPagination(nPage) {
            const response = await api(`/api/employees?company=/api/companies/${this.companyID}&page=${nPage}`, 'GET')
            this.employees = await this.updatePagination(response)
        },
        async paginationSortableOrFilterItems(payload) {
            let response = {}
            if (payload.filter.value === true && payload.sortable.value === true){
                let url = `/api/employees?company=/api/companies/${this.companyID}&`
                if (payload.filterBy.value.name !== '') {
                    url += `name=${payload.filterBy.value.name}&`
                }
                if (payload.filterBy.value.surname !== '') {
                    url += `surname=${payload.filterBy.value.surname}&`
                }
                if (payload.filterBy.value.notes !== '') {
                    url += `notes=${payload.filterBy.value.notes}&`
                }
                if (payload.filterBy.value.entryDate !== '') {
                    url += `entryDate=${payload.filterBy.value.entryDate}&`
                }
                if (payload.filterBy.value.username !== '') {
                    url += `username=${payload.filterBy.value.username}&`
                }
                if (payload.filterBy.value.initials !== '') {
                    url += `initials=${payload.filterBy.value.initials}&`
                }
                response = await api(url, 'GET')
                this.employees = await this.updatePagination(response)
            } else if (payload.filter.value === true){
                let url = `/api/employees?company=/api/companies/${this.companyID}&`
                if (payload.filterBy.value.name !== '') {
                    url += `name=${payload.filterBy.value.name}&`
                }
                if (payload.filterBy.value.surname !== '') {
                    url += `surname=${payload.filterBy.value.surname}&`
                }
                if (payload.filterBy.value.notes !== '') {
                    url += `notes=${payload.filterBy.value.notes}&`
                }
                if (payload.filterBy.value.entryDate !== '') {
                    url += `entryDate=${payload.filterBy.value.entryDate}&`
                }
                if (payload.filterBy.value.username !== '') {
                    url += `username=${payload.filterBy.value.username}&`
                }
                if (payload.filterBy.value.initials !== '') {
                    url += `initials=${payload.filterBy.value.initials}&`
                }
                url += `page=${payload.nPage}`
                response = await api(url, 'GET')
                this.employees = await this.updatePagination(response)
            } else if (payload.sortable.value === false) {
                response = await api(`/api/employees?company=/api/companies/${this.companyID}&page=${payload.nPage}`, 'GET')
                this.employees = await this.updatePagination(response)
            } else {
                if (payload.trierAlpha.value.composant === 'company') {
                    response = await api(`/api/employees?company=/api/companies/${this.companyID}&order%5B${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                } else {
                    response = await api(`/api/employees?company=/api/companies/${this.companyID}&order%5Baddress.${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                }
                this.employees = await this.updatePagination(response)
            }
        },

        async sortableItems(payload, filterBy, filter) {
            let response = {}
            if (filter.value === true){
                if (filterBy.value.name !== '') {
                    url += `name=${filterBy.value.name}&`
                }
                if (filterBy.value.surname !== '') {
                    url += `surname=${filterBy.value.surname}&`
                }
                if (filterBy.value.notes !== '') {
                    url += `notes=${filterBy.value.notes}&`
                }
                if (filterBy.value.entryDate !== '') {
                    url += `entryDate=${filterBy.value.entryDate}&`
                }
                if (filterBy.value.username !== '') {
                    url += `username=${filterBy.value.username}&`
                }
                if (filterBy.value.initials !== '') {
                    url += `initials=${filterBy.value.initials}&`
                }
                url += `page=${this.currentPage}`
                response = await api(url, 'GET')
                this.employees = await this.updatePagination(response)
            } else {
                if (payload.composant === 'company') {
                    response = await api(`/api/employees?company=/api/companies/${this.companyID}&order%5B${payload.composant}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                } else {
                    response = await api(`/api/employees?company=/api/companies/${this.companyID}&order%5B${payload.produit}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                }
                this.employees = await this.updatePagination(response)
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
        itemsCompanyEmploye: state => state.employees.map(item => {
            let dt = ''
            if (item.entryDate !== null){
                dt = item.entryDate.split('T')[0]
            }
            const newObject = {
                '@id': `${item['@id']}`,
                name: item.name,
                surname: item.surname,
                entryDate: dt,
                notes: item.notes,
                username: item.username,
                initials: item.initials
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
        employees: [],
        companyID: 0
    })
})
