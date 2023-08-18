import api from '../../../api'
import {defineStore} from 'pinia'

export const useComponentListReferenceValueStore = defineStore('componentListReferenceValue', {
    actions: {
        setIdComponent(id){
            this.componentID = id
        },
        // async addReferenceValueReferenceValue(payload){
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
            await api(`/api/manufacturing-orders/${payload}`, 'DELETE')
            this.componentReferenceValue = this.componentReferenceValue.filter(retard => Number(retard['@id'].match(/\d+/)[0]) !== payload)
        },
        async fetch() {
            if (this.currentPage < 1){
                this.currentPage = 1
            }
            const response = await api(`/api/component-reference-values?component=/api/components/${this.componentID}?page=${this.currentPage}`, 'GET')
            this.componentReferenceValue = await this.updatePagination(response)
        },
        async filterBy(payload) {
            let url = `/api/component-reference-values?component=/api/components/${this.componentID}&`
            if (payload === ''){
                await this.fetch()
            } else {
                if (payload.height !== ''){
                    if (payload.height.value !== '') {
                        url += `height.value.value=${payload.height.value}&`
                    }
                    if (payload.height.code !== '') {
                        url += `height.value.code=${payload.height.code}&`
                    }
                }
                if (payload.section !== ''){
                    if (payload.section.value !== '') {
                        url += `section.value=${payload.section.value}&`
                    }
                    if (payload.section.code !== '') {
                        url += `section.code=${payload.section.code}&`
                    }
                }
                if (payload.tensile !== ''){
                    if (payload.tensile.value !== '') {
                        url += `tensile.value.value=${payload.tensile.value}&`
                    }
                    if (payload.tensile.code !== '') {
                        url += `tensile.value.code=${payload.tensile.code}&`
                    }
                }
                if (payload.width !== ''){
                    if (payload.width.value !== '') {
                        url += `width.value.value=${payload.width.value}&`
                    }
                    if (payload.width.code !== '') {
                        url += `width.value.code=${payload.width.code}&`
                    }
                }
                url += 'page=1'
                this.currentPage = 1
                const response = await api(url, 'GET')
                this.componentReferenceValue = await this.updatePagination(response)
            }
        },
        async itemsPagination(nPage) {
            const response = await api(`/api/component-reference-values?component=/api/components/${this.componentID}?page=${this.currentPage}&page=${nPage}`, 'GET')
            this.componentReferenceValue = await this.updatePagination(response)
        },
        async paginationSortableOrFilterItems(payload) {
            let response = {}
            if (payload.filter.value === true && payload.sortable.value === true){
                let url = `/api/component-reference-values?component=/api/components/${this.componentID}?page=${this.currentPage}&`
                if (payload.filterBy.value.of !== '') {
                    url += `productReferenceValue=${payload.filterBy.value.of}&`
                }
                if (payload.filterBy.value.produit !== '') {
                    url += `name=${payload.filterBy.value.produit}&`
                }
                if (payload.filterBy.value.date !== '') {
                    url += `date=${payload.filterBy.value.date}&`
                }
                if (payload.filterBy.value.quantiteComposant !== ''){
                    if (payload.filterBy.value.quantiteComposant.value !== '') {
                        url += `forecastVolumeValue=${payload.filterBy.value.quantiteComposant.value}&`
                    }
                    if (payload.filterBy.value.quantiteComposant.code !== '') {
                        url += `forecastVolumeCode=${payload.filterBy.value.quantiteComposant.code}&`
                    }
                }

                response = await api(url, 'GET')
                this.componentReferenceValue = await this.updatePagination(response)
            } else if (payload.filter.value === true){
                let url = `/api/component-reference-values?component=/api/components/${this.componentID}?page=${this.currentPage}&`
                if (payload.filterBy.value.of !== '') {
                    url += `productReferenceValue=${payload.filterBy.value.of}&`
                }
                if (payload.filterBy.value.produit !== '') {
                    url += `name=${payload.filterBy.value.produit}&`
                }
                if (payload.filterBy.value.date !== '') {
                    url += `date=${payload.filterBy.value.date}&`
                }
                if (payload.filterBy.value.quantiteComposant !== ''){
                    if (payload.filterBy.value.quantiteComposant.value !== '') {
                        url += `forecastVolumeValue=${payload.filterBy.value.quantiteComposant.value}&`
                    }
                    if (payload.filterBy.value.quantiteComposant.code !== '') {
                        url += `forecastVolumeCode=${payload.filterBy.value.quantiteComposant.code}&`
                    }
                }

                url += `page=${payload.nPage}`
                response = await api(url, 'GET')
                this.componentReferenceValue = await this.updatePagination(response)
            } else if (payload.sortable.value === false) {
                response = await api(`/api/component-reference-values?component=/api/components/${this.componentID}?page=${this.currentPage}&page=${payload.nPage}`, 'GET')
                this.componentReferenceValue = await this.updatePagination(response)
            } else {
                if (payload.trierAlpha.value.composant === 'component') {
                    response = await api(`/api/component-reference-values?component=/api/components/${this.componentID}?page=${this.currentPage}&order%5B${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                } else {
                    response = await api(`/api/component-reference-values?component=/api/components/${this.componentID}?page=${this.currentPage}&order%5Baddress.${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                }
                this.componentReferenceValue = await this.updatePagination(response)
            }
        },

        async sortableItems(payload, filterBy, filter) {
            let response = {}
            if (filter.value === true){
                if (filterBy.value.of !== '') {
                    url += `productReferenceValue=${filterBy.value.of}&`
                }
                if (payload.filterBy.value.produit !== '') {
                    url += `name=${filterBy.value.produit}&`
                }
                if (filterBy.value.date !== '') {
                    url += `date=${filterBy.value.date}&`
                }
                if (filterBy.value.quantiteComposant !== ''){
                    if (filterBy.value.quantiteComposant.value !== '') {
                        url += `forecastVolumeValue=${filterBy.value.quantiteComposant.value}&`
                    }
                    if (filterBy.value.quantiteComposant.code !== '') {
                        url += `forecastVolumeCode=${filterBy.value.quantiteComposant.code}&`
                    }
                }
                url += `page=${this.currentPage}`
                response = await api(url, 'GET')
                this.componentReferenceValue = await this.updatePagination(response)
            } else {
                if (payload.composant === 'component') {
                    response = await api(`/api/component-reference-values?component=/api/components/${this.componentID}?page=${this.currentPage}&order%5B${payload.composant}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                } else {
                    response = await api(`/api/component-reference-values?component=/api/components/${this.componentID}?page=${this.currentPage}&order%5B${payload.produit}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                }
                this.componentReferenceValue = await this.updatePagination(response)
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
        itemsComponentReferenceValue: state => state.componentReferenceValue.map(item => {
            const newObject = {
                '@id': item['@id'],
                height: `${item.height.value.value} ${item.height.value.code}`,
                section: `${item.section.value} ${item.section.code}`,
                tensile: `${item.tensile.value.value} ${item.tensile.value.code}`,
                width: `${item.width.value.value} ${item.width.value.code}`
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
        componentReferenceValue: [],
        componentID: 0
    })
})
