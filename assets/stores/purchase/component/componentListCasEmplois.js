import api from '../../../api'
import {defineStore} from 'pinia'

export const useComponentListCasEmploisStore = defineStore('componentListCasEmplois', {
    actions: {
        setIdComponent(id){
            this.componentID = id
        },
        // async addCasEmploisCasEmplois(payload){
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
            await api(`/api/nomenclatures/${payload}`, 'DELETE')
            this.componentCasEmplois = this.componentCasEmplois.filter(retard => Number(retard['@id'].match(/\d+/)[0]) !== payload)
        },
        async fetch() {
            if (this.currentPage < 1){
                this.currentPage = 1
            }
            const response = await api(`/api/nomenclatures?component=/api/components/${this.componentID}`, 'GET')
            this.componentCasEmplois = await this.updatePagination(response)
        },
        async filterBy(payload) {
            let url = `/api/nomenclatures?component=/api/components/${this.componentID}&`
            if (payload === ''){
                await this.fetch()
            } else {
                if (payload.ref !== '') {
                    url += `product.product.code=${payload.ref}&`
                }
                if (payload.name !== '') {
                    url += `product.product.name=${payload.name}&`
                }
                if (payload.etat !== '' && payload.etat.state !== '') {
                    url += `product.product.embState.state=${payload.etat}&`
                }
                if (payload.client !== '') {
                    url += `product.customer.name=${payload.client}&`
                }
                if (payload.volumeAnnuel !== ''){
                    if (payload.volumeAnnuel.value !== '') {
                        url += `product.product.forecastVolume.value=${payload.volumeAnnuel.value}&`
                    }
                    if (payload.volumeAnnuel.code !== '') {
                        url += `product.product.forecastVolume.code=${payload.volumeAnnuel.code}&`
                    }
                }
                if (payload.quantiteProduit !== '') {
                    url += `product.product.internalIndex=${payload.quantiteProduit}&`
                }
                url += 'page=1'
                this.currentPage = 1
                const response = await api(url, 'GET')
                this.componentCasEmplois = await this.updatePagination(response)
            }
        },
        async itemsPagination(nPage) {
            const response = await api(`/api/nomenclatures?component=/api/components/${this.componentID}&page=${nPage}`, 'GET')
            this.componentCasEmplois = await this.updatePagination(response)
        },
        async paginationSortableOrFilterItems(payload) {
            let response = {}
            if (payload.filter.value === true && payload.sortable.value === true){
                let url = `/api/nomenclatures?component=/api/components/${this.componentID}&`
                if (payload.filterBy.value.ref !== '') {
                    url += `product.product.code=${payload.filterBy.value.ref}&`
                }
                if (payload.filterBy.value.name !== '') {
                    url += `product.product.name=${payload.filterBy.value.name}&`
                }
                if (payload.filterBy.value.etat !== '' && payload.filterBy.value.etat.state !== '') {
                    url += `product.product.embState.state=${payload.filterBy.value.etat}&`
                }
                if (payload.filterBy.value.client !== '') {
                    url += `product.customer.name=${payload.filterBy.value.client}&`
                }
                if (payload.filterBy.value.volumeAnnuel !== ''){
                    if (payload.filterBy.value.volumeAnnuel.value !== '') {
                        url += `product.product.forecastVolume.value=${payload.filterBy.value.volumeAnnuel.value}&`
                    }
                    if (payload.filterBy.value.volumeAnnuel.code !== '') {
                        url += `product.product.forecastVolume.code=${payload.filterBy.value.volumeAnnuel.code}&`
                    }
                }
                if (payload.filterBy.value.quantiteProduit !== '') {
                    url += `product.product.internalIndex=${payload.filterBy.value.quantiteProduit}&`
                }

                response = await api(url, 'GET')
                this.componentCasEmplois = await this.updatePagination(response)
            } else if (payload.filter.value === true){
                let url = `/api/nomenclatures?component=/api/components/${this.componentID}&`
                if (payload.filterBy.value.ref !== '') {
                    url += `product.product.code=${payload.filterBy.value.ref}&`
                }
                if (payload.filterBy.value.name !== '') {
                    url += `product.product.name=${payload.filterBy.value.name}&`
                }
                if (payload.filterBy.value.etat !== '' && payload.filterBy.value.etat.state !== '') {
                    url += `product.product.embState.state=${payload.filterBy.value.etat}&`
                }
                if (payload.filterBy.value.client !== '') {
                    url += `product.customer.name=${payload.filterBy.value.client}&`
                }
                if (payload.filterBy.value.volumeAnnuel !== ''){
                    if (payload.filterBy.value.volumeAnnuel.value !== '') {
                        url += `product.product.forecastVolume.value=${payload.filterBy.value.volumeAnnuel.value}&`
                    }
                    if (payload.filterBy.value.volumeAnnuel.code !== '') {
                        url += `product.product.forecastVolume.code=${payload.filterBy.value.volumeAnnuel.code}&`
                    }
                }
                if (payload.filterBy.value.quantiteProduit !== '') {
                    url += `product.product.internalIndex=${payload.filterBy.value.quantiteProduit}&`
                }
                url += `page=${payload.nPage}`
                response = await api(url, 'GET')
                this.componentCasEmplois = await this.updatePagination(response)
            } else if (payload.sortable.value === false) {
                response = await api(`/api/nomenclatures?component=/api/components/${this.componentID}&page=${payload.nPage}`, 'GET')
                this.componentCasEmplois = await this.updatePagination(response)
            } else {
                if (payload.trierAlpha.value.composant === 'component') {
                    response = await api(`/api/nomenclatures?component=/api/components/${this.componentID}&order%5B${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                } else {
                    response = await api(`/api/nomenclatures?component=/api/components/${this.componentID}&order%5Baddress.${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                }
                this.componentCasEmplois = await this.updatePagination(response)
            }
        },

        async sortableItems(payload, filterBy, filter) {
            let response = {}
            if (filter.value === true){
                if (filterBy.value.ref !== '') {
                    url += `product.product.code=${filterBy.value.ref}&`
                }
                if (filterBy.value.name !== '') {
                    url += `product.product.name=${filterBy.value.name}&`
                }
                if (filterBy.value.etat !== '' && filterBy.value.etat.state !== '') {
                    url += `product.product.embState.state=${filterBy.value.etat}&`
                }
                if (filterBy.value.client !== '') {
                    url += `product.customer.name=${filterBy.value.client}&`
                }
                if (filterBy.value.volumeAnnuel !== ''){
                    if (filterBy.value.volumeAnnuel.value !== '') {
                        url += `product.product.forecastVolume.value=${filterBy.value.volumeAnnuel.value}&`
                    }
                    if (filterBy.value.volumeAnnuel.code !== '') {
                        url += `product.product.forecastVolume.code=${filterBy.value.volumeAnnuel.code}&`
                    }
                }
                if (filterBy.value.quantiteProduit !== '') {
                    url += `product.product.internalIndex=${filterBy.value.quantiteProduit}&`
                }
                url += `page=${this.currentPage}`
                response = await api(url, 'GET')
                this.componentCasEmplois = await this.updatePagination(response)
            } else {
                if (payload.composant === 'component') {
                    response = await api(`/api/nomenclatures?component=/api/components/${this.componentID}&order%5B${payload.composant}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                } else {
                    response = await api(`/api/nomenclatures?component=/api/components/${this.componentID}&order%5B${payload.produit}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                }
                this.componentCasEmplois = await this.updatePagination(response)
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
        itemsComponentCasEmplois: state => state.componentCasEmplois.map(item => {
            const nbAnnuelComposant = item.product.product.forecastVolume.value * item.product.product.internalIndex
            const newObject = {
                '@id': item['@id'],
                ref: item.product.product.code,
                name: item.product.product.name,
                etat: item.product.product.embState.state,
                client: item.product.customer.name,
                volumeAnnuel: `${item.product.product.forecastVolume.value} ${item.product.product.forecastVolume.code}`,
                quantiteProduit: item.product.product.internalIndex,
                volumeAnnuelComposant: `${nbAnnuelComposant} ${item.product.product.forecastVolume.code}`
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
        componentCasEmplois: [],
        componentID: 0
    })
})
