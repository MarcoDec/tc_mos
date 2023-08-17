import api from '../../api'
import {defineStore} from 'pinia'

export const useCompanyListUserAbilityStore = defineStore('equipementListUserAbility', {
    actions: {
        setIdCompany(id){
            this.equipementID = id
        },
        // async addUserAbilityUserAbility(payload){
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
            this.equipementUserAbility = this.equipementUserAbility.filter(retard => Number(retard['@id'].match(/\d+/)[0]) !== payload)
        },
        async fetch() {
            if (this.currentPage < 1){
                this.currentPage = 1
            }
            const response = await api(`/api/engine-events/filtreEmployee/${this.equipementID}?`, 'GET')
            this.equipementUserAbility = await this.updatePagination(response)
        },
        async filterBy(payload) {
            let url = `/api/engine-events/filtreEmployee/${this.equipementID}?`
            if (payload === ''){
                await this.fetch()
            } else {
                if (payload.name !== '') {
                    url += `name=${payload.name}&`
                }
                if (payload.surname !== '') {
                    url += `surname=${payload.surname}&`
                }
                url += 'page=1'
                this.currentPage = 1
                const response = await api(url, 'GET')
                this.equipementUserAbility = await this.updatePagination(response)
            }
        },
        async itemsPagination(nPage) {
            const response = await api(`/api/engine-events/filtreEmployee/${this.equipementID}?page=${nPage}`, 'GET')
            this.equipementUserAbility = await this.updatePagination(response)
        },
        async paginationSortableOrFilterItems(payload) {
            let response = {}
            if (payload.filter.value === true && payload.sortable.value === true){
                let url = `/api/engine-events/filtreEmployee/${this.equipementID}?`
                if (payload.filterBy.value.name !== '') {
                    url += `name=${payload.filterBy.value.name}&`
                }
                if (payload.filterBy.value.surname !== '') {
                    url += `surname=${payload.filterBy.value.surname}&`
                }
                response = await api(url, 'GET')
                this.equipementUserAbility = await this.updatePagination(response)
            } else if (payload.filter.value === true){
                let url = `/api/engine-events/filtreEmployee/${this.equipementID}?`
                if (payload.filterBy.value.name !== '') {
                    url += `name=${payload.filterBy.value.name}&`
                }
                if (payload.filterBy.value.surnamename !== '') {
                    url += `surname=${payload.filterBy.value.surname}&`
                }
                url += `page=${payload.nPage}`
                response = await api(url, 'GET')
                this.equipementUserAbility = await this.updatePagination(response)
            } else if (payload.sortable.value === false) {
                response = await api(`/api/engine-events/filtreEmployee/${this.equipementID}?page=${payload.nPage}`, 'GET')
                this.equipementUserAbility = await this.updatePagination(response)
            } else {
                if (payload.trierAlpha.value.composant === 'equipement') {
                    response = await api(`/api/engine-events/filtreEmployee/${this.equipementID}?order%5B${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                } else {
                    response = await api(`/api/engine-events/filtreEmployee/${this.equipementID}?order%5Baddress.${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                }
                this.equipementUserAbility = await this.updatePagination(response)
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
                url += `page=${this.currentPage}`
                response = await api(url, 'GET')
                this.equipementUserAbility = await this.updatePagination(response)
            } else {
                if (payload.composant === 'equipement') {
                    response = await api(`/api/engine-events/filtreEmployee/${this.equipementID}?order%5B${payload.composant}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                } else {
                    response = await api(`/api/engine-events/filtreEmployee/${this.equipementID}?order%5B${payload.produit}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                }
                this.equipementUserAbility = await this.updatePagination(response)
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
        itemsEquipementUserAbility: state => state.equipementUserAbility.map(item => {
            const newObject = {
                '@id': `${item['@id']}`,
                name: item.name,
                surname: item.surname
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
        equipementUserAbility: [],
        equipementID: 0
    })
})
