import api from '../../../api'
import {defineStore} from 'pinia'

export const useEmployeeListEvenementStore = defineStore('employeeListEvenement', {
    actions: {
        setIdEmployee(id){
            this.employeeID = id
        },
        async deleted(payload) {
            await api(`/api/employee-events/${payload}`, 'DELETE')
            this.employeeEvenement = this.employeeEvenement.filter(retard => Number(retard['@id'].match(/\d+/)[0]) !== payload)
        },
        async fetch(criteria = '?page=1') {
            // const response = await api(`/api/selling-order-items?employee=${this.employeeID}`, 'GET')
            if (this.currentPage < 1){
                this.currentPage = 1
            }
            // const response = await api(`/api/selling-order-items/employeeFilter/${this.employeeID}?page=${this.currentPage}`, 'GET')
            const response = await api(`/api/employee-events${criteria}`, 'GET')
            this.employeeEvenement = await this.updatePagination(response)
        },
        async filterBy(payload) {
            let url = `/api/employee-events?employee=/api/employees/${this.employeeID}&`
            if (payload === ''){
                await this.fetch()
            } else {
                if (payload.date !== '') {
                    url += `date=${payload.date}&`
                }

                if (payload.motif !== ''){
                    url += `type.name=${payload.motif}&`
                }

                if (payload.description !== ''){
                    url += `name=${payload.description}&`
                }

                url += 'page=1'
                this.currentPage = 1
                const response = await api(url, 'GET')
                this.employeeEvenement = await this.updatePagination(response)
            }
        },
        async itemsPagination(nPage) {
            const response = await api(`/api/employee-events?employee=/api/employees/${this.employeeID}?page=${nPage}`, 'GET')
            this.employeeEvenement = await this.updatePagination(response)
        },
        async paginationSortableOrFilterItems(payload) {
            let response = {}
            if (payload.filter.value === true && payload.sortable.value === true){
                if (payload.trierAlpha.value.component === 'component') {
                    let url = `/api/employee-events?employee=/api/employees/${this.employeeID}&`
                    if (payload.filterBy.value.date !== '') {
                        url += `date=${payload.filterBy.value.date}&`
                    }

                    if (payload.filterBy.value.motif !== ''){
                        url += `type.name=${payload.filterBy.value.motif}&`
                    }

                    if (payload.filterBy.value.description !== ''){
                        url += `name=${payload.filterBy.value.description}&`
                    }
                    url += `page=${payload.nPage}`

                    response = await api(url, 'GET')
                    this.employeeEvenement = await this.updatePagination(response)
                } else {
                    let url = `/api/employee-events?employee=/api/employees/${this.employeeID}&`
                    if (payload.filterBy.value.date !== '') {
                        url += `date=${payload.filterBy.value.date}&`
                    }

                    if (payload.filterBy.value.motif !== ''){
                        url += `type.name=${payload.filterBy.value.motif}&`
                    }

                    if (payload.filterBy.value.description !== ''){
                        url += `name=${payload.filterBy.value.description}&`
                    }

                    response = await api(url, 'GET')

                    this.employeeEvenement = await this.updatePagination(response)
                }
            } else if (payload.filter.value === true){
                let url = `/api/employee-events?employee=/api/employees/${this.employeeID}&`
                if (payload.filterBy.value.date !== '') {
                    url += `date=${payload.filterBy.value.date}&`
                }

                if (payload.filterBy.value.motif !== ''){
                    url += `type.name=${payload.filterBy.value.motif}&`
                }

                if (payload.filterBy.value.description !== ''){
                    url += `name=${payload.filterBy.value.description}&`
                }
                url += `page=${payload.nPage}`
                response = await api(url, 'GET')
                this.employeeEvenement = await this.updatePagination(response)
            } else if (payload.sortable.value === false) {
                response = await api(`/api/employee-events?employee=/api/employees/${this.employeeID}&page=${payload.nPage}`, 'GET')
                this.employeeEvenement = await this.updatePagination(response)
            } else {
                if (payload.trierAlpha.value.composant === 'component') {
                    response = await api(`/api/employee-events?employee=/api/employees/${this.employeeID}&order%5B${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                } else {
                    response = await api(`/api/employee-events?employee=/api/employees/${this.employeeID}&order%5Baddress.${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                }
                this.employeeEvenement = await this.updatePagination(response)
            }
        },

        async sortableItems(payload, filterBy, filter) {
            let response = {}
            if (filter.value === true){
                if (payload.composant === 'component') {
                    let url = `/api/employee-events?employee=/api/employees/${this.employeeID}&`
                    if (filterBy.value === ''){
                        await this.fetch()
                    } else {
                        if (filterBy.value.date !== '') {
                            url += `date=${filterBy.value.date}&`
                        }
                        if (filterBy.value.motif !== ''){
                            url += `type.name=${filterBy.value.motif}&`
                        }
                        if (filterBy.value.description !== ''){
                            url += `name=${filterBy.value.description}&`
                        }
                    }
                    url += `page=${this.currentPage}`
                    response = await api(url, 'GET')
                } else {
                    let url = `/api/employee-events?employee=/api/employees/${this.employeeID}&`
                    if (filterBy.value === ''){
                        await this.fetch()
                    } else {
                        if (filterBy.value.date !== '') {
                            url += `date=${filterBy.value.date}&`
                        }

                        if (filterBy.value.motif !== ''){
                            url += `type.name=${filterBy.value.motif}&`
                        }

                        if (filterBy.value.description !== ''){
                            url += `name=${filterBy.value.description}&`
                        }
                    }
                    url += `page=${this.currentPage}`
                    response = await api(url, 'GET')
                }
                this.employeeEvenement = await this.updatePagination(response)
            } else {
                if (payload.composant === 'component') {
                    response = await api(`/api/employee-events?employee=/api/employees/${this.employeeID}&order%5B${payload.composant}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                } else {
                    response = await api(`/api/employee-events?employee=/api/employees/${this.employeeID}&order%5B${payload.produit}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                }
                this.employeeEvenement = await this.updatePagination(response)
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
        itemsEmployeeEvenement: state => state.employeeEvenement.map(item => {
            let dt = ''
            if (item.date !== null) {
                dt = item.date.split('T')[0]
            }
            const newObject = {
                '@id': item['@id'],
                date: dt,
                type: item.type,
                name: item.name,
                employee: item.employee
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
        employeeEvenement: [],
        employeeID: 0
    })
})
