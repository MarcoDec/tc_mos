import api from '../../api'
import {defineStore} from 'pinia'

export const useCompanyListEventStore = defineStore('equipementListEvent', {
    actions: {
        setIdCompany(id){
            this.equipementID = id
        },
        // async addEventEvent(payload){
        //     const violations = []
        //     try {
        //         if (payload.quantite.value !== ''){
        //             payload.quantite.value = parseInt(payload.quantite.value)
        //         }
        //         const element = {
        //             equipement: payload.composant,
        //             refFournisseur: payload.refFournisseur,
        //             prix: payload.prix,
        //             quantity: payload.quantite,
        //             texte: payload.texte
        //         }
        //         await api('/api/equipement-stocks', 'POST', element)
        //         this.fetch()
        //     } catch (error) {
        //         violations.push({message: error})
        //     }
        //     return violations
        // },
        async deleted(payload) {
            await api(`/api/engine-events/${payload}`, 'DELETE')
            this.equipementEvent = this.equipementEvent.filter(retard => Number(retard['@id'].match(/\d+/)[0]) !== payload)
        },
        async fetch() {
            if (this.currentPage < 1){
                this.currentPage = 1
            }
            const response = await api(`/api/engine-events/filtreEvent/${this.equipementID}?`, 'GET')
            this.equipementEvent = await this.updatePagination(response)
        },
        async filterBy(payload) {
            let url = `/api/engine-events/filtreEvent/${this.equipementID}?`
            if (payload === ''){
                await this.fetch()
            } else {
                if (payload.date !== '') {
                    url += `date=${payload.date}&`
                }
                if (payload.done !== '') {
                    url += `done=${payload.done}&`
                }
                if (payload.done !== '') {
                    if (payload.done === true){
                        url += 'done=1&'
                    } else {
                        url += 'done=0&'
                    }
                }
                if (payload.emergency !== '') {
                    url += `emergency=${payload.emergency}&`
                }
                if (payload.etat !== '') {
                    url += `etat=${payload.etat}&`
                }
                if (payload.interventionNotes !== '') {
                    url += `interventionNotes=${payload.interventionNotes}&`
                }
                if (payload.type !== '') {
                    url += `type=${payload.type}&`
                }
                url += 'page=1'
                this.currentPage = 1
                const response = await api(url, 'GET')
                this.equipementEvent = await this.updatePagination(response)
            }
        },
        async itemsPagination(nPage) {
            const response = await api(`/api/engine-events/filtreEvent/${this.equipementID}?page=${nPage}`, 'GET')
            this.equipementEvent = await this.updatePagination(response)
        },
        async paginationSortableOrFilterItems(payload) {
            let response = {}
            if (payload.filter.value === true && payload.sortable.value === true){
                let url = `/api/engine-events/filtreEvent/${this.equipementID}?`
                if (payload.filterBy.value.date !== '') {
                    url += `date=${payload.filterBy.value.date}&`
                }
                if (payload.filterBy.value.done !== '') {
                    url += `done=${payload.filterBy.value.done}&`
                }
                if (payload.filterBy.value.done !== '') {
                    if (payload.filterBy.value.done === true){
                        url += 'done=1&'
                    } else {
                        url += 'done=0&'
                    }
                }
                if (payload.filterBy.value.emergency !== '') {
                    url += `emergency=${payload.filterBy.value.emergency}&`
                }
                if (payload.filterBy.value.etat !== '') {
                    url += `etat=${payload.filterBy.value.etat}&`
                }
                if (payload.filterBy.value.interventionNotes !== '') {
                    url += `interventionNotes=${payload.filterBy.value.interventionNotes}&`
                }
                if (payload.filterBy.value.type !== '') {
                    url += `type=${payload.filterBy.value.type}&`
                }
                response = await api(url, 'GET')
                this.equipementEvent = await this.updatePagination(response)
            } else if (payload.filter.value === true){
                let url = `/api/engine-events/filtreEvent/${this.equipementID}?`
                if (payload.filterBy.value.date !== '') {
                    url += `date=${payload.filterBy.value.date}&`
                }
                if (payload.filterBy.value.done !== '') {
                    url += `done=${payload.filterBy.value.done}&`
                }
                if (payload.filterBy.value.done !== '') {
                    if (payload.filterBy.value.done === true){
                        url += 'done=1&'
                    } else {
                        url += 'done=0&'
                    }
                }
                if (payload.filterBy.value.emergency !== '') {
                    url += `emergency=${payload.filterBy.value.emergency}&`
                }
                if (payload.filterBy.value.etat !== '') {
                    url += `etat=${payload.filterBy.value.etat}&`
                }
                if (payload.filterBy.value.interventionNotes !== '') {
                    url += `interventionNotes=${payload.filterBy.value.interventionNotes}&`
                }
                if (payload.filterBy.value.type !== '') {
                    url += `type=${payload.filterBy.value.type}&`
                }
                url += `page=${payload.nPage}`
                response = await api(url, 'GET')
                this.equipementEvent = await this.updatePagination(response)
            } else if (payload.sortable.value === false) {
                response = await api(`/api/engine-events/filtreEvent/${this.equipementID}?page=${payload.nPage}`, 'GET')
                this.equipementEvent = await this.updatePagination(response)
            } else {
                if (payload.trierAlpha.value.composant === 'equipement') {
                    response = await api(`/api/engine-events/filtreEvent/${this.equipementID}?order%5B${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                } else {
                    response = await api(`/api/engine-events/filtreEvent/${this.equipementID}?order%5Baddress.${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                }
                this.equipementEvent = await this.updatePagination(response)
            }
        },

        async sortableItems(payload, filterBy, filter) {
            let response = {}
            if (filter.value === true){
                if (filterBy.value.date !== '') {
                    url += `date=${filterBy.value.date}&`
                }
                if (filterBy.value.done !== '') {
                    url += `done=${filterBy.value.done}&`
                }
                if (filterBy.value.done !== '') {
                    if (filterBy.value.done === true){
                        url += 'done=1&'
                    } else {
                        url += 'done=0&'
                    }
                }
                if (filterBy.value.emergency !== '') {
                    url += `emergency=${filterBy.value.emergency}&`
                }
                if (payload.filterBy.value.etat !== '') {
                    url += `etat=${payload.filterBy.value.etat}&`
                }
                if (filterBy.value.interventionNotes !== '') {
                    url += `interventionNotes=${filterBy.value.interventionNotes}&`
                }
                if (filterBy.value.type !== '') {
                    url += `type=${filterBy.value.type}&`
                }
                url += `page=${this.currentPage}`
                response = await api(url, 'GET')
                this.equipementEvent = await this.updatePagination(response)
            } else {
                if (payload.composant === 'equipement') {
                    response = await api(`/api/engine-events/filtreEvent/${this.equipementID}?order%5B${payload.composant}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                } else {
                    response = await api(`/api/engine-events/filtreEvent/${this.equipementID}?order%5B${payload.produit}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                }
                this.equipementEvent = await this.updatePagination(response)
            }
        },
        async updatePagination(response) {
            const responseData = await response['hydra:member']
            if (responseData.length === 3 && responseData[1] > 1) {
                this.pagination = true
                this.firstPage = 1
                this.currentPage = responseData[0]
                this.lastPage = responseData[1]
                if (this.currentPage >= this.lastPage){
                    this.nextPage = this.lastPage
                } else {
                    this.nextPage = parseInt(responseData[0]) + 1
                }
                if (this.currentPage <= this.firstPage) {
                    this.previousPage = this.firstPage
                } else {
                    this.previousPage = this.currentPage - 1
                }
                return responseData[2]
            }
            this.pagination = false
            return responseData[2]
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
        itemsEquipementEvent: state => state.equipementEvent.map(item => {
            const dt = item.date.split(' ')[0]
            const newObject = {
                '@id': `${item['@id']}`,
                date: dt,
                done: item.done,
                emergency: item.emergency,
                etat: item.etat,
                interventionNotes: item.interventionNotes,
                type: item.type
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
        equipementEvent: [],
        equipementID: 0
    })
})
