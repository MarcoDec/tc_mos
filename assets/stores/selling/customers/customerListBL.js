import api from '../../../api'
import {defineStore} from 'pinia'

export const useCustomerListBLStore = defineStore('customerListBL', {
    actions: {
        setIdCustomer(id){
            this.customerID = id
        },
        // async addBLBL(payload){
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
            await api(`/api/expeditions/${payload}`, 'DELETE')
            this.customerBL = this.customerBL.filter(retard => Number(retard['@id'].match(/\d+/)[0]) !== payload)
        },
        async fetch() {
            // const response = await api(`/api/selling-order-items?customer=${this.customerID}`, 'GET')
            if (this.currentPage < 1){
                this.currentPage = 1
            }
            // const response = await api(`/api/selling-order-items/customerFilter/${this.customerID}?page=${this.currentPage}`, 'GET')
            const response = await api(`/api/expeditions?item.item.customer.id=${this.customerID}&order%5Bnote.date%5D=desc`, 'GET')
            this.customerBL = await this.updatePagination(response)
        },
        async filterBy(payload) {
            let url = `/api/expeditions?item.item.customer.id=${this.customerID}&order%5Bnote.date%5D=desc&`
            if (payload === ''){
                await this.fetch()
            } else {
                if (payload.ref !== '') {
                    url += `note.ref=${payload.ref}&`
                }

                if (payload.etat !== '' && payload.etat.state !== '') {
                    url += `note.embState.state=${payload.etat}&`
                }

                if (payload.date !== '') {
                    url += `note.date=${payload.date}&`
                }

                if (payload.supplementFret !== ''){
                    if (payload.supplementFret.value !== '') {
                        url += `note.freightSurcharge.value=${payload.supplementFret.value}&`
                    }
                    if (payload.supplementFret.code !== '') {
                        url += `note.freightSurcharge.code=${payload.supplementFret.code}&`
                    }
                }

                if (payload.noBl !== '') {
                    if (payload.noBl === true){
                        url += 'note.nonBillable=1&'
                    } else {
                        url += 'note.nonBillable=0&'
                    }
                }
                url += 'page=1'
                this.currentPage = 1
                const response = await api(url, 'GET')
                this.customerBL = await this.updatePagination(response)
            }
        },
        async itemsPagination(nPage) {
            const response = await api(`/api/expeditions?item.item.customer.id=${this.customerID}&order%5Bnote.date%5D=desc&page=${nPage}`, 'GET')
            this.customerBL = await this.updatePagination(response)
        },
        async paginationSortableOrFilterItems(payload) {
            let response = {}
            if (payload.filter.value === true && payload.sortable.value === true){
                let url = `/api/expeditions?item.item.customer.id=${this.customerID}&order%5Bnote.date%5D=desc&`
                if (payload.filterBy.value.ref !== '') {
                    url += `note.ref=${payload.filterBy.value.ref}&`
                }

                if (payload.filterBy.value.etat !== '') {
                    url += `note.embState.state=${payload.filterBy.value.etat}&`
                }

                if (payload.filterBy.value.date !== '') {
                    url += `note.date=${payload.filterBy.value.date}&`
                }

                if (payload.filterBy.value.supplementFret !== ''){
                    if (payload.filterBy.value.supplementFret.value !== '') {
                        url += `note.freightSurcharge.value=${payload.filterBy.value.supplementFret.value}&`
                    }
                    if (payload.filterBy.value.supplementFret.code !== '') {
                        url += `note.freightSurcharge.code=${payload.filterBy.value.supplementFret.code}&`
                    }
                }

                if (payload.filterBy.value.noBl !== '') {
                    if (payload.filterBy.value.noBl === true){
                        url += 'note.nonBillable=1&'
                    } else {
                        url += 'note.nonBillable=0&'
                    }
                }

                response = await api(url, 'GET')
                this.customerBL = await this.updatePagination(response)
            } else if (payload.filter.value === true){
                let url = `/api/expeditions?item.item.customer.id=${this.customerID}&order%5Bnote.date%5D=desc&`
                if (payload.filterBy.value.ref !== '') {
                    url += `note.ref=${payload.filterBy.value.ref}&`
                }

                if (payload.filterBy.value.etat !== '') {
                    url += `note.embState.state=${payload.filterBy.value.etat}&`
                }

                if (payload.filterBy.value.date !== '') {
                    url += `note.date=${payload.filterBy.value.date}&`
                }

                if (payload.filterBy.value.supplementFret !== ''){
                    if (payload.filterBy.value.supplementFret.value !== '') {
                        url += `note.freightSurcharge.value=${payload.filterBy.value.supplementFret.value}&`
                    }
                    if (payload.filterBy.value.supplementFret.code !== '') {
                        url += `note.freightSurcharge.code=${payload.filterBy.value.supplementFret.code}&`
                    }
                }

                if (payload.filterBy.value.noBl !== '') {
                    if (payload.filterBy.value.noBl === true){
                        url += 'note.nonBillable=1&'
                    } else {
                        url += 'note.nonBillable=0&'
                    }
                }
                url += `page=${payload.nPage}`
                response = await api(url, 'GET')
                this.customerBL = await this.updatePagination(response)
            } else if (payload.sortable.value === false) {
                response = await api(`/api/expeditions?item.item.customer.id=${this.customerID}&order%5Bnote.date%5D=desc&page=${payload.nPage}`, 'GET')
                this.customerBL = await this.updatePagination(response)
            } else {
                if (payload.trierAlpha.value.composant === 'component') {
                    response = await api(`/api/expeditions?item.item.customer.id=${this.customerID}&order%5Bnote.date%5D=desc&order%5B${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                } else {
                    response = await api(`/api/expeditions?item.item.customer.id=${this.customerID}&order%5Bnote.date%5D=desc&order%5Baddress.${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                }
                this.customerBL = await this.updatePagination(response)
            }
        },

        async sortableItems(payload, filterBy, filter) {
            let response = {}
            if (filter.value === true){
                if (filterBy.value.ref !== '') {
                    url += `note.ref=${filterBy.value.ref}&`
                }

                if (filterBy.value.etat !== '') {
                    url += `note.embState.state=${filterBy.value.etat}&`
                }

                if (filterBy.value.date !== '') {
                    url += `note.date=${filterBy.value.date}&`
                }

                if (filterBy.value.supplementFret !== ''){
                    if (filterBy.value.supplementFret.value !== '') {
                        url += `note.freightSurcharge.value=${filterBy.value.supplementFret.value}&`
                    }
                    if (filterBy.value.supplementFret.code !== '') {
                        url += `note.freightSurcharge.code=${filterBy.value.supplementFret.code}&`
                    }
                }

                if (filterBy.value.noBl !== '') {
                    if (filterBy.value.noBl === true){
                        url += 'note.nonBillable=1&'
                    } else {
                        url += 'note.nonBillable=0&'
                    }
                }
                url += `page=${this.currentPage}`
                response = await api(url, 'GET')
                this.customerBL = await this.updatePagination(response)
            } else {
                if (payload.composant === 'component') {
                    response = await api(`/api/expeditions?item.item.customer.id=${this.customerID}&order%5Bnote.date%5D=desc&order%5B${payload.composant}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                } else {
                    response = await api(`/api/expeditions?item.item.customer.id=${this.customerID}&order%5Bnote.date%5D=desc&order%5B${payload.produit}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                }
                this.customerBL = await this.updatePagination(response)
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
        itemsCustomerBL: state => state.customerBL.map(item => {
            const dt = item.note.date.split('T')[0]
            const newObject = {
                '@id': item['@id'],
                ref: item.note.ref,
                name: null,
                etat: item.note.embState.state,
                date: dt,
                supplementFret: `${item.note.freightSurcharge.value} ${item.note.freightSurcharge.code}`,
                noBl: item.note.nonBillable
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
        customerBL: [],
        customerID: 0
    })
})
