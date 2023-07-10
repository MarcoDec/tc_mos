import api from '../../../../api'
import {defineStore} from 'pinia'

export const useEmployeeListClockingStore = defineStore('employeeListClocking', {
    actions: {
        setIdEmployee(id){
            this.employeeID = id
        },
        // async addEmployeeClocking(payload){
        //     const violations = []
        //     try {
        //         if (payload.quantite.value !== ''){
        //             payload.quantite.value = parseInt(payload.quantite.value)
        //         }
        //         const element = {
        //             component: payload.composant,
        //             refFournisseur: payload.refFournisseur,
        //             prix: payload.prix,
        //             quantity: payload.quantite,
        //             texte: payload.texte
        //         }
        //         await api('/api/component-stocks', 'POST', element)
        //         this.fetch()
        //     } catch (error) {
        //         violations.push({message: error})
        //     }
        //     return violations
        // },
        async deleted(payload) {
            await api(`/api/clockings/${payload}`, 'DELETE')
            this.employeeClocking = this.employeeClocking.filter(retard => Number(retard['@id'].match(/\d+/)[0]) !== payload)
        },
        async fetch() {
            // const response = await api(`/api/selling-order-items?employee=${this.employeeID}`, 'GET')
            if (this.currentPage < 1){
                this.currentPage = 1
            }
            // const response = await api(`/api/selling-order-items/employeeFilter/${this.employeeID}?page=${this.currentPage}`, 'GET')
            const response = await api(`/api/clockings?employee=/api/employees/${this.employeeID}`, 'GET')
            this.employeeClocking = await this.updatePagination(response)
        },
        async filterBy(payload) {
            let url = `/api/clockings?employee=/api/employees/${this.employeeID}&`
            if (payload === ''){
                await this.fetch()
            } else {
                if (payload.creationDate !== '') {
                    url += `creationDate=${payload.creationDate}&`
                }

                if (payload.date !== ''){
                    url += `date=${payload.date}&`
                }

                if (payload.enter !== ''){
                    if (payload.enter === true){
                        url += 'enter=1&'
                    } else {
                        url += 'enter=0&'
                    }
                }

                url += 'page=1'
                this.currentPage = 1
                const response = await api(url, 'GET')
                this.employeeClocking = await this.updatePagination(response)
            }
        },
        async itemsPagination(nPage) {
            const response = await api(`/api/selling-order-items/employeeFilter/${this.employeeID}?page=${nPage}`, 'GET')
            this.employeeClocking = await this.updatePagination(response)
        },
        async paginationSortableOrFilterItems(payload) {
            let response = {}
            if (payload.filter.value === true && payload.sortable.value === true){
                if (payload.trierAlpha.value.component === 'component') {
                    let url = `/api/clockings?employee=/api/employees/${this.employeeID}&`
                    if (payload.filterBy.value.creationDate !== '') {
                        url += `creationDate=${payload.filterBy.value.creationDate}&`
                    }

                    if (payload.filterBy.value.date !== ''){
                        url += `date=${payload.filterBy.value.date}&`
                    }

                    if (payload.filterBy.value.enter !== ''){
                        if (payload.filterBy.value.enter === true){
                            url += 'enter=1&'
                        } else {
                            url += 'enter=0&'
                        }
                    }
                    url += `page=${payload.nPage}`

                    response = await api(url, 'GET')
                    this.employeeClocking = await this.updatePagination(response)
                } else {
                    let url = `/api/clockings?employee=/api/employees/${this.employeeID}&`
                    if (payload.filterBy.value.creationDate !== '') {
                        url += `creationDate=${payload.filterBy.value.creationDate}&`
                    }

                    if (payload.filterBy.value.date !== ''){
                        url += `date=${payload.filterBy.value.date}&`
                    }

                    if (payload.filterBy.value.enter !== ''){
                        if (payload.filterBy.value.enter === true){
                            url += 'enter=1&'
                        } else {
                            url += 'enter=0&'
                        }
                    }
                    response = await api(url, 'GET')

                    this.employeeClocking = await this.updatePagination(response)
                }
            } else if (payload.filter.value === true){
                let url = `/api/clockings?employee=/api/employees/${this.employeeID}&`
                if (payload.filterBy.value.creationDate !== '') {
                    url += `creationDate=${payload.filterBy.value.creationDate}&`
                }

                if (payload.filterBy.value.date !== ''){
                    url += `date=${payload.filterBy.value.date}&`
                }

                if (payload.filterBy.value.enter !== ''){
                    if (payload.filterBy.value.enter === true){
                        url += 'enter=1&'
                    } else {
                        url += 'enter=0&'
                    }
                }
                url += `page=${payload.nPage}`
                response = await api(url, 'GET')
                this.employeeClocking = await this.updatePagination(response)
            } else if (payload.sortable.value === false) {
                response = await api(`/api/clockings?employee=/api/employees/${this.employeeID}&page=${payload.nPage}`, 'GET')
                this.employeeClocking = await this.updatePagination(response)
            } else {
                if (payload.trierAlpha.value.composant === 'component') {
                    response = await api(`/api/clockings?employee=/api/employees/${this.employeeID}&order%5B${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                } else {
                    response = await api(`/api/clockings?employee=/api/employees/${this.employeeID}&order%5Baddress.${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                }
                this.employeeClocking = await this.updatePagination(response)
            }
        },

        async sortableItems(payload, filterBy, filter) {
            let response = {}
            if (filter.value === true){
                if (payload.composant === 'component') {
                    let url = `/api/clockings?employee=/api/employees/${this.employeeID}&`
                    if (filterBy.value === ''){
                        await this.fetch()
                    } else {
                        if (filterBy.value.creationDate !== '') {
                            url += `creationDate=${filterBy.value.creationDate}&`
                        }

                        if (filterBy.value.date !== ''){
                            url += `date=${filterBy.value.date}&`
                        }

                        if (filterBy.value.enter !== ''){
                            if (filterBy.value.enter === true){
                                url += 'enter=1&'
                            } else {
                                url += 'enter=0&'
                            }
                        }
                    }
                    url += `page=${this.currentPage}`
                    response = await api(url, 'GET')
                } else {
                    let url = `/api/clockings?employee=/api/employees/${this.employeeID}&`
                    if (filterBy.value === ''){
                        await this.fetch()
                    } else {
                        if (filterBy.value.creationDate !== '') {
                            url += `creationDate=${filterBy.value.creationDate}&`
                        }

                        if (filterBy.value.date !== ''){
                            url += `date=${filterBy.value.date}&`
                        }

                        if (filterBy.value.enter !== ''){
                            if (filterBy.value.enter === true){
                                url += 'enter=1&'
                            } else {
                                url += 'enter=0&'
                            }
                        }
                    }
                    url += `page=${this.currentPage}`
                    response = await api(url, 'GET')
                }
                this.employeeClocking = await this.updatePagination(response)
            } else {
                if (payload.composant === 'component') {
                    response = await api(`/api/clockings?employee=/api/employees/${this.employeeID}&order%5B${payload.composant}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                } else {
                    response = await api(`/api/clockings?employee=/api/employees/${this.employeeID}&order%5B${payload.produit}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                }
                this.employeeClocking = await this.updatePagination(response)
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
        itemsEmployeeClocking: state => state.employeeClocking.map(item => {
            const createDt = item.creationDate.replace(/T/g, ' ')
            const dt = item.date.replace(/T/g, ' ')
            const newObject = {
                '@id': item['@id'],
                creationDate: createDt,
                date: dt,
                enter: item.enter
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
        employeeClocking: [],
        employeeID: 0
    })
})
