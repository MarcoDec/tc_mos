import api from '../../../api'
import {defineStore} from 'pinia'

export const useSocietyListCompanyStore = defineStore('societyListCompany', {
    actions: {
        setIdSociety(id){
            this.societyID = id
        },
        // async addSocietyCompany(payload){
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
            await api(`/api/companies/${payload}`, 'DELETE')
            this.societyCompany = this.societyCompany.filter(retard => Number(retard['@id'].match(/\d+/)[0]) !== payload)
        },
        async fetch() {
            // const response = await api(`/api/selling-order-items?society=${this.societyID}`, 'GET')
            if (this.currentPage < 1){
                this.currentPage = 1
            }
            // const response = await api(`/api/selling-order-items/societyFilter/${this.societyID}?page=${this.currentPage}`, 'GET')
            const response = await api(`/api/companies?society.id=${this.societyID}`, 'GET')
            this.societyCompany = await this.updatePagination(response)
        },
        async filterBy(payload) {
            let url = `/api/companies?society.id=${this.societyID}&`
            if (payload === ''){
                await this.fetch()
            } else {
                if (payload.name !== '') {
                    url += `name=${payload.name}&`
                }

                if (payload.deliveryTime !== ''){
                    url += `deliveryTime=${payload.deliveryTime}&`
                }
                if (payload.deliveryTimeOpenDays !== ''){
                    if (payload.deliveryTimeOpenDays === true){
                        url += 'deliveryTimeOpenDays=1&'
                    } else {
                        url += 'deliveryTimeOpenDays=0&'
                    }
                }
                if (payload.engineHourRate !== ''){
                    url += `engineHourRate=${payload.engineHourRate}&`
                }

                if (payload.generalMargin !== ''){
                    url += `generalMargin=${payload.generalMargin}&`
                }
                if (payload.handlingHourRate !== ''){
                    url += `handlingHourRate=${payload.handlingHourRate}&`
                }

                if (payload.managementFees !== ''){
                    url += `managementFees=${payload.managementFees}&`
                }
                if (payload.numberOfTeamPerDay !== ''){
                    url += `numberOfTeamPerDay=${payload.numberOfTeamPerDay}&`
                }

                url += 'page=1'
                this.currentPage = 1
                const response = await api(url, 'GET')
                this.societyCompany = await this.updatePagination(response)
            }
        },
        async itemsPagination(nPage) {
            const response = await api(`/api/selling-order-items/societyFilter/${this.societyID}?page=${nPage}`, 'GET')
            this.societyCompany = await this.updatePagination(response)
        },
        async paginationSortableOrFilterItems(payload) {
            let response = {}
            if (payload.filter.value === true && payload.sortable.value === true){
                let url = `/api/companies?society.id=${this.societyID}&`
                if (payload.filterBy.value.name !== '') {
                    url += `name=${payload.filterBy.value.name}&`
                }
                if (payload.filterBy.value.deliveryTime !== ''){
                    url += `deliveryTime=${payload.filterBy.value.deliveryTime}&`
                }
                if (payload.filterBy.value.deliveryTimeOpenDays !== ''){
                    if (payload.filterBy.value.deliveryTimeOpenDays === true){
                        url += 'deliveryTimeOpenDays=1&'
                    } else {
                        url += 'deliveryTimeOpenDays=0&'
                    }
                }
                if (payload.filterBy.value.engineHourRate !== ''){
                    url += `engineHourRate=${payload.filterBy.value.engineHourRate}&`
                }

                if (payload.filterBy.value.generalMargin !== ''){
                    url += `generalMargin=${payload.filterBy.value.generalMargin}&`
                }
                if (payload.filterBy.value.handlingHourRate !== ''){
                    url += `handlingHourRate=${payload.filterBy.value.handlingHourRate}&`
                }

                if (payload.filterBy.value.managementFees !== ''){
                    url += `managementFees=${payload.filterBy.value.managementFees}&`
                }
                if (payload.filterBy.value.numberOfTeamPerDay !== ''){
                    url += `numberOfTeamPerDay=${payload.filterBy.value.numberOfTeamPerDay}&`
                }

                response = await api(url, 'GET')
                this.societyCompany = await this.updatePagination(response)
            } else if (payload.filter.value === true){
                let url = `/api/companies?society.id=${this.societyID}&`
                if (payload.filterBy.value.name !== '') {
                    url += `name=${payload.filterBy.value.name}&`
                }
                if (payload.filterBy.value.deliveryTime !== ''){
                    url += `deliveryTime=${payload.filterBy.value.deliveryTime}&`
                }
                if (payload.filterBy.value.deliveryTimeOpenDays !== ''){
                    if (payload.filterBy.value.deliveryTimeOpenDays === true){
                        url += 'deliveryTimeOpenDays=1&'
                    } else {
                        url += 'deliveryTimeOpenDays=0&'
                    }
                }
                if (payload.filterBy.value.engineHourRate !== ''){
                    url += `engineHourRate=${payload.filterBy.value.engineHourRate}&`
                }

                if (payload.filterBy.value.generalMargin !== ''){
                    url += `generalMargin=${payload.filterBy.value.generalMargin}&`
                }
                if (payload.filterBy.value.handlingHourRate !== ''){
                    url += `handlingHourRate=${payload.filterBy.value.handlingHourRate}&`
                }

                if (payload.filterBy.value.managementFees !== ''){
                    url += `managementFees=${payload.filterBy.value.managementFees}&`
                }
                if (payload.filterBy.value.numberOfTeamPerDay !== ''){
                    url += `numberOfTeamPerDay=${payload.filterBy.value.numberOfTeamPerDay}&`
                }
                url += `page=${payload.nPage}`
                response = await api(url, 'GET')
                this.societyCompany = await this.updatePagination(response)
            } else if (payload.sortable.value === false) {
                response = await api(`/api/companies?society.id=${this.societyID}&page=${payload.nPage}`, 'GET')
                this.societyCompany = await this.updatePagination(response)
            } else {
                if (payload.trierAlpha.value.composant === 'component') {
                    response = await api(`/api/companies?society.id=${this.societyID}&order%5B${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                } else {
                    response = await api(`/api/companies?society.id=${this.societyID}&order%5Baddress.${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                }
                this.societyCompany = await this.updatePagination(response)
            }
        },

        async sortableItems(payload, filterBy, filter) {
            let response = {}
            if (filter.value === true){
                let url = `/api/companies?society.id=${this.societyID}&`
                if (filterBy.value.name !== '') {
                    url += `name=${filterBy.value.name}&`
                }
                if (filterBy.value.deliveryTime !== ''){
                    url += `deliveryTime=${filterBy.value.deliveryTime}&`
                }
                if (filterBy.value.deliveryTimeOpenDays !== ''){
                    if (filterBy.value.deliveryTimeOpenDays === true){
                        url += 'deliveryTimeOpenDays=1&'
                    } else {
                        url += 'deliveryTimeOpenDays=0&'
                    }
                }
                if (filterBy.value.engineHourRate !== ''){
                    url += `engineHourRate=${filterBy.value.engineHourRate}&`
                }

                if (filterBy.value.generalMargin !== ''){
                    url += `generalMargin=${filterBy.value.generalMargin}&`
                }
                if (filterBy.value.handlingHourRate !== ''){
                    url += `handlingHourRate=${filterBy.value.handlingHourRate}&`
                }

                if (filterBy.value.managementFees !== ''){
                    url += `managementFees=${filterBy.value.managementFees}&`
                }
                if (filterBy.value.numberOfTeamPerDay !== ''){
                    url += `numberOfTeamPerDay=${filterBy.value.numberOfTeamPerDay}&`
                }
                url += `page=${this.currentPage}`
                response = await api(url, 'GET')
                this.societyCompany = await this.updatePagination(response)
            } else {
                if (payload.composant === 'component') {
                    response = await api(`/api/companies?society.id=${this.societyID}&order%5B${payload.composant}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                } else {
                    response = await api(`/api/companies?society.id=${this.societyID}&order%5B${payload.produit}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                }
                this.societyCompany = await this.updatePagination(response)
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
        itemsSocietyCompany: state => state.societyCompany.map(item => {
            console.log(item)
            const newObject = {
                '@id': item['@id'],
                name: item.name,
                deliveryTime: item.deliveryTime,
                deliveryTimeOpenDays: item.deliveryTimeOpenDays,
                engineHourRate: item.engineHourRate,
                generalMargin: item.generalMargin,
                handlingHourRate: item.handlingHourRate,
                managementFees: item.managementFees,
                numberOfTeamPerDay: item.numberOfTeamPerDay
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
        societyCompany: [],
        societyID: 0
    })
})
