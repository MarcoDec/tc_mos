import api from '../../../api'
import {defineStore} from 'pinia'

export const useComponentListBesoinEnregistreStore = defineStore('componentListBesoinEnregistre', {
    actions: {
        setIdComponent(id){
            this.componentID = id
        },
        // async addBesoinEnregistreBesoinEnregistre(payload){
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
            this.componentBesoinEnregistre = this.componentBesoinEnregistre.filter(retard => Number(retard['@id'].match(/\d+/)[0]) !== payload)
        },
        async fetch() {
            if (this.currentPage < 1){
                this.currentPage = 1
            }
            const response = await api(`/api/nomenclatures?component=/api/components/${this.componentID}`, 'GET')
            this.componentBesoinEnregistre = await this.updatePagination(response)
        },
        async filterBy(payload) {
            let url = `/api/nomenclatures?component=/api/components/${this.componentID}&`
            if (payload === ''){
                await this.fetch()
            } else {
                if (payload.refVente !== '') {
                    url += `product.product.code=${payload.refVente}&`
                }
                if (payload.ref !== '') {
                    url += `product.product.name=${payload.ref}&`
                }
                if (payload.date !== '') {
                    url += `product.customer.name=${payload.date}&`
                }
                if (payload.consomationStock !== '') {
                    url += `product.product.internalIndex=${payload.consomationStock}&`
                }

                if (payload.besoinProd !== '') {
                    url += `product.product.internalIndex=${payload.besoinProd}&`
                }
                if (payload.besoinComponent !== '') {
                    url += `product.product.internalIndex=${payload.besoinComponent}&`
                }
                if (payload.stockReel !== ''){
                    if (payload.stockReel.value !== '') {
                        url += `product.product.forecastVolume.value=${payload.stockReel.value}&`
                    }
                    if (payload.stockReel.code !== '') {
                        url += `product.product.forecastVolume.code=${payload.stockReel.code}&`
                    }
                }
                if (payload.stockDispo !== ''){
                    if (payload.stockDispo.value !== '') {
                        url += `product.product.forecastVolume.value=${payload.stockDispo.value}&`
                    }
                    if (payload.stockDispo.code !== '') {
                        url += `product.product.forecastVolume.code=${payload.stockDispo.code}&`
                    }
                }
                if (payload.quantiteProduit !== ''){
                    if (payload.quantiteProduit.value !== '') {
                        url += `product.product.forecastVolume.value=${payload.quantiteProduit.value}&`
                    }
                    if (payload.quantiteProduit.code !== '') {
                        url += `product.product.forecastVolume.code=${payload.quantiteProduit.code}&`
                    }
                }
                if (payload.quantiteAExpedier !== ''){
                    if (payload.quantiteAExpedier.value !== '') {
                        url += `product.product.forecastVolume.value=${payload.quantiteAExpedier.value}&`
                    }
                    if (payload.quantiteAExpedier.code !== '') {
                        url += `product.product.forecastVolume.code=${payload.quantiteAExpedier.code}&`
                    }
                }

                url += 'page=1'
                this.currentPage = 1
                const response = await api(url, 'GET')
                this.componentBesoinEnregistre = await this.updatePagination(response)
            }
        },
        async itemsPagination(nPage) {
            const response = await api(`/api/nomenclatures?component=/api/components/${this.componentID}&page=${nPage}`, 'GET')
            this.componentBesoinEnregistre = await this.updatePagination(response)
        },
        async paginationSortableOrFilterItems(payload) {
            let response = {}
            if (payload.filter.value === true && payload.sortable.value === true){
                let url = `/api/nomenclatures?component=/api/components/${this.componentID}&`
                if (payload.filterBy.value.refVente !== '') {
                    url += `product.product.code=${payload.filterBy.value.refVente}&`
                }
                if (payload.filterBy.value.ref !== '') {
                    url += `product.product.name=${payload.filterBy.value.ref}&`
                }
                if (payload.filterBy.value.date !== '') {
                    url += `product.customer.name=${payload.filterBy.value.date}&`
                }
                if (payload.filterBy.value.consomationStock !== '') {
                    url += `product.product.internalIndex=${payload.filterBy.value.consomationStock}&`
                }

                if (payload.filterBy.value.besoinProd !== '') {
                    url += `product.product.internalIndex=${payload.filterBy.value.besoinProd}&`
                }
                if (payload.filterBy.value.besoinComponent !== '') {
                    url += `product.product.internalIndex=${payload.filterBy.value.besoinComponent}&`
                }
                if (payload.filterBy.value.stockReel !== ''){
                    if (payload.filterBy.value.stockReel.value !== '') {
                        url += `product.product.forecastVolume.value=${payload.filterBy.value.stockReel.value}&`
                    }
                    if (payload.filterBy.value.stockReel.code !== '') {
                        url += `product.product.forecastVolume.code=${payload.filterBy.value.stockReel.code}&`
                    }
                }
                if (payload.filterBy.value.stockDispo !== ''){
                    if (payload.filterBy.value.stockDispo.value !== '') {
                        url += `product.product.forecastVolume.value=${payload.filterBy.value.stockDispo.value}&`
                    }
                    if (payload.filterBy.value.stockDispo.code !== '') {
                        url += `product.product.forecastVolume.code=${payload.filterBy.value.stockDispo.code}&`
                    }
                }
                if (payload.filterBy.value.quantiteProduit !== ''){
                    if (payload.filterBy.value.quantiteProduit.value !== '') {
                        url += `product.product.forecastVolume.value=${payload.filterBy.value.quantiteProduit.value}&`
                    }
                    if (payload.filterBy.value.quantiteProduit.code !== '') {
                        url += `product.product.forecastVolume.code=${payload.filterBy.value.quantiteProduit.code}&`
                    }
                }
                if (payload.filterBy.value.quantiteAExpedier !== ''){
                    if (payload.filterBy.value.quantiteAExpedier.value !== '') {
                        url += `product.product.forecastVolume.value=${payload.filterBy.value.quantiteAExpedier.value}&`
                    }
                    if (payload.filterBy.value.quantiteAExpedier.code !== '') {
                        url += `product.product.forecastVolume.code=${payload.filterBy.value.quantiteAExpedier.code}&`
                    }
                }

                response = await api(url, 'GET')
                this.componentBesoinEnregistre = await this.updatePagination(response)
            } else if (payload.filter.value === true){
                let url = `/api/nomenclatures?component=/api/components/${this.componentID}&`
                if (payload.filterBy.value.refVente !== '') {
                    url += `product.product.code=${payload.filterBy.value.refVente}&`
                }
                if (payload.filterBy.value.ref !== '') {
                    url += `product.product.name=${payload.filterBy.value.ref}&`
                }
                if (payload.filterBy.value.date !== '') {
                    url += `product.customer.name=${payload.filterBy.value.date}&`
                }
                if (payload.filterBy.value.consomationStock !== '') {
                    url += `product.product.internalIndex=${payload.filterBy.value.consomationStock}&`
                }

                if (payload.filterBy.value.besoinProd !== '') {
                    url += `product.product.internalIndex=${payload.filterBy.value.besoinProd}&`
                }
                if (payload.filterBy.value.besoinComponent !== '') {
                    url += `product.product.internalIndex=${payload.filterBy.value.besoinComponent}&`
                }
                if (payload.filterBy.value.stockReel !== ''){
                    if (payload.filterBy.value.stockReel.value !== '') {
                        url += `product.product.forecastVolume.value=${payload.filterBy.value.stockReel.value}&`
                    }
                    if (payload.filterBy.value.stockReel.code !== '') {
                        url += `product.product.forecastVolume.code=${payload.filterBy.value.stockReel.code}&`
                    }
                }
                if (payload.filterBy.value.stockDispo !== ''){
                    if (payload.filterBy.value.stockDispo.value !== '') {
                        url += `product.product.forecastVolume.value=${payload.filterBy.value.stockDispo.value}&`
                    }
                    if (payload.filterBy.value.stockDispo.code !== '') {
                        url += `product.product.forecastVolume.code=${payload.filterBy.value.stockDispo.code}&`
                    }
                }
                if (payload.filterBy.value.quantiteProduit !== ''){
                    if (payload.filterBy.value.quantiteProduit.value !== '') {
                        url += `product.product.forecastVolume.value=${payload.filterBy.value.quantiteProduit.value}&`
                    }
                    if (payload.filterBy.value.quantiteProduit.code !== '') {
                        url += `product.product.forecastVolume.code=${payload.filterBy.value.quantiteProduit.code}&`
                    }
                }
                if (payload.filterBy.value.quantiteAExpedier !== ''){
                    if (payload.filterBy.value.quantiteAExpedier.value !== '') {
                        url += `product.product.forecastVolume.value=${payload.filterBy.value.quantiteAExpedier.value}&`
                    }
                    if (payload.filterBy.value.quantiteAExpedier.code !== '') {
                        url += `product.product.forecastVolume.code=${payload.filterBy.value.quantiteAExpedier.code}&`
                    }
                }

                url += `page=${payload.nPage}`
                response = await api(url, 'GET')
                this.componentBesoinEnregistre = await this.updatePagination(response)
            } else if (payload.sortable.value === false) {
                response = await api(`/api/nomenclatures?component=/api/components/${this.componentID}&page=${payload.nPage}`, 'GET')
                this.componentBesoinEnregistre = await this.updatePagination(response)
            } else {
                if (payload.trierAlpha.value.composant === 'component') {
                    response = await api(`/api/nomenclatures?component=/api/components/${this.componentID}&order%5B${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                } else {
                    response = await api(`/api/nomenclatures?component=/api/components/${this.componentID}&order%5Baddress.${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                }
                this.componentBesoinEnregistre = await this.updatePagination(response)
            }
        },

        async sortableItems(payload, filterBy, filter) {
            let response = {}
            if (filter.value === true){
                if (filterBy.value.refVente !== '') {
                    url += `product.product.code=${filterBy.value.refVente}&`
                }
                if (filterBy.value.ref !== '') {
                    url += `product.product.name=${filterBy.value.ref}&`
                }
                if (filterBy.value.date !== '') {
                    url += `product.customer.name=${filterBy.value.date}&`
                }
                if (filterBy.value.consomationStock !== '') {
                    url += `product.product.internalIndex=${filterBy.value.consomationStock}&`
                }

                if (filterBy.value.besoinProd !== '') {
                    url += `product.product.internalIndex=${filterBy.value.besoinProd}&`
                }
                if (filterBy.value.besoinComponent !== '') {
                    url += `product.product.internalIndex=${filterBy.value.besoinComponent}&`
                }
                if (filterBy.value.stockReel !== ''){
                    if (filterBy.value.stockReel.value !== '') {
                        url += `product.product.forecastVolume.value=${filterBy.value.stockReel.value}&`
                    }
                    if (filterBy.value.stockReel.code !== '') {
                        url += `product.product.forecastVolume.code=${filterBy.value.stockReel.code}&`
                    }
                }
                if (filterBy.value.stockDispo !== ''){
                    if (filterBy.value.stockDispo.value !== '') {
                        url += `product.product.forecastVolume.value=${filterBy.value.stockDispo.value}&`
                    }
                    if (filterBy.value.stockDispo.code !== '') {
                        url += `product.product.forecastVolume.code=${filterBy.value.stockDispo.code}&`
                    }
                }
                if (filterBy.value.quantiteProduit !== ''){
                    if (filterBy.value.quantiteProduit.value !== '') {
                        url += `product.product.forecastVolume.value=${filterBy.value.quantiteProduit.value}&`
                    }
                    if (filterBy.value.quantiteProduit.code !== '') {
                        url += `product.product.forecastVolume.code=${filterBy.value.quantiteProduit.code}&`
                    }
                }
                if (filterBy.value.quantiteAExpedier !== ''){
                    if (filterBy.value.quantiteAExpedier.value !== '') {
                        url += `product.product.forecastVolume.value=${filterBy.value.quantiteAExpedier.value}&`
                    }
                    if (filterBy.value.quantiteAExpedier.code !== '') {
                        url += `product.product.forecastVolume.code=${filterBy.value.quantiteAExpedier.code}&`
                    }
                }

                url += `page=${this.currentPage}`
                response = await api(url, 'GET')
                this.componentBesoinEnregistre = await this.updatePagination(response)
            } else {
                if (payload.composant === 'component') {
                    response = await api(`/api/nomenclatures?component=/api/components/${this.componentID}&order%5B${payload.composant}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                } else {
                    response = await api(`/api/nomenclatures?component=/api/components/${this.componentID}&order%5B${payload.produit}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                }
                this.componentBesoinEnregistre = await this.updatePagination(response)
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
        itemsComponentBesoinEnregistre: state => state.componentBesoinEnregistre.map(item => {
            const newObject = {
                '@id': item['@id'],
                refVente: null,
                ref: null,
                stockReel: null,
                stockDispo: null,
                date: null,
                quantiteProduit: null,
                quantiteAExpedier: null,
                consomationStock: null,
                besoinProd: null,
                besoinComponent: null
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
        componentBesoinEnregistre: [],
        componentID: 0
    })
})
