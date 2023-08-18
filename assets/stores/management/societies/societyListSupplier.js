import api from '../../api'
import {defineStore} from 'pinia'

export const useSocietyListSupplierStore = defineStore('societyListSupplier', {
    actions: {
        setIdSociety(id){
            this.societyID = id
        },
        // async addSocietySupplier(payload){
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
            await api(`/api/suppliers/${payload}`, 'DELETE')
            this.societySupplier = this.societySupplier.filter(retard => Number(retard['@id'].match(/\d+/)[0]) !== payload)
        },
        async fetch() {
            // const response = await api(`/api/selling-order-items?society=${this.societyID}`, 'GET')
            if (this.currentPage < 1){
                this.currentPage = 1
            }
            // const response = await api(`/api/selling-order-items/societyFilter/${this.societyID}?page=${this.currentPage}`, 'GET')
            const response = await api(`/api/suppliers?society.id=${this.societyID}`, 'GET')
            this.societySupplier = await this.updatePagination(response)
        },
        async filterBy(payload) {
            let url = `/api/suppliers?society.id=${this.societyID}&`
            if (payload === ''){
                await this.fetch()
            } else {
                if (payload.name !== '') {
                    url += `name=${payload.name}&`
                }

                if (payload.city !== ''){
                    url += `address.city=${payload.city}&`
                }

                if (payload.country !== '') {
                    url += `address.country=${payload.country}&`
                }

                if (payload.email !== ''){
                    url += `address.email=${payload.email}&`
                }

                if (payload.phoneNumber !== ''){
                    url += `address.phoneNumber=${payload.phoneNumber}&`
                }

                url += 'page=1'
                this.currentPage = 1
                const response = await api(url, 'GET')
                this.societySupplier = await this.updatePagination(response)
            }
        },
        async itemsPagination(nPage) {
            const response = await api(`/api/selling-order-items/societyFilter/${this.societyID}?page=${nPage}`, 'GET')
            this.societySupplier = await this.updatePagination(response)
        },
        async paginationSortableOrFilterItems(payload) {
            let response = {}
            if (payload.filter.value === true && payload.sortable.value === true){
                let url = `/api/suppliers?society.id=${this.societyID}&`
                if (payload.filterBy.value.name !== '') {
                    url += `name=${payload.filterBy.value.name}&`
                }
                if (payload.filterBy.value.city !== ''){
                    url += `address.city=${payload.filterBy.value.city}&`
                }
                if (payload.filterBy.value.country !== '') {
                    url += `address.country=${payload.filterBy.value.country}&`
                }
                if (payload.filterBy.value.email !== ''){
                    url += `address.email=${payload.filterBy.value.email}&`
                }
                if (payload.filterBy.value.phoneNumber !== '') {
                    url += `address.phoneNumber=${payload.filterBy.value.phoneNumber}&`
                }

                response = await api(url, 'GET')
                this.societySupplier = await this.updatePagination(response)
            } else if (payload.filter.value === true){
                let url = `/api/suppliers?society.id=${this.societyID}&`
                if (payload.filterBy.value.name !== '') {
                    url += `name=${payload.filterBy.value.name}&`
                }
                if (payload.filterBy.value.city !== ''){
                    url += `address.city=${payload.filterBy.value.city}&`
                }
                if (payload.filterBy.value.country !== '') {
                    url += `address.country=${payload.filterBy.value.country}&`
                }
                if (payload.filterBy.value.email !== ''){
                    url += `address.email=${payload.filterBy.value.email}&`
                }
                if (payload.filterBy.value.phoneNumber !== '') {
                    url += `address.phoneNumber=${payload.filterBy.value.phoneNumber}&`
                }
                url += `page=${payload.nPage}`
                response = await api(url, 'GET')
                this.societySupplier = await this.updatePagination(response)
            } else if (payload.sortable.value === false) {
                response = await api(`/api/suppliers?society.id=${this.societyID}&page=${payload.nPage}`, 'GET')
                this.societySupplier = await this.updatePagination(response)
            } else {
                if (payload.trierAlpha.value.composant === 'component') {
                    response = await api(`/api/suppliers?society.id=${this.societyID}&order%5B${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                } else {
                    response = await api(`/api/suppliers?society.id=${this.societyID}&order%5Baddress.${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                }
                this.societySupplier = await this.updatePagination(response)
            }
        },

        async sortableItems(payload, filterBy, filter) {
            let response = {}
            if (filter.value === true){
                let url = `/api/suppliers?society.id=${this.societyID}&`
                if (filterBy.value.name !== '') {
                    url += `name=${filterBy.value.name}&`
                }
                if (filterBy.value.city !== ''){
                    url += `address.city=${filterBy.value.city}&`
                }
                if (filterBy.value.country !== '') {
                    url += `address.country=${filterBy.value.country}&`
                }
                if (filterBy.value.email !== ''){
                    url += `address.email=${filterBy.value.email}&`
                }
                if (filterBy.value.phoneNumber !== '') {
                    url += `address.phoneNumber=${filterBy.value.phoneNumber}&`
                }
                url += `page=${this.currentPage}`
                response = await api(url, 'GET')
                this.societySupplier = await this.updatePagination(response)
            } else {
                if (payload.composant === 'component') {
                    response = await api(`/api/suppliers?society.id=${this.societyID}&order%5B${payload.composant}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                } else {
                    response = await api(`/api/suppliers?society.id=${this.societyID}&order%5B${payload.produit}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                }
                this.societySupplier = await this.updatePagination(response)
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
        itemsSocietySupplier: state => state.societySupplier.map(item => {
            const newObject = {
                '@id': item['@id'],
                name: item.name,
                city: item.address.city,
                country: item.address.country,
                email: item.address.email,
                phoneNumber: item.address.phoneNumber
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
        societySupplier: [],
        societyID: 0
    })
})
