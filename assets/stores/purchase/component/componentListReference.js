import api from '../../../api'
import {defineStore} from 'pinia'

export const useComponentListReferenceStore = defineStore('componentListReference', {
    actions: {
        setIdComponent(id){
            this.componentID = id
        },
        getUniqueProductCodes(response) {
            const uniqueCodesSet = new Set()
            if (response && response['hydra:member']) {
                response['hydra:member'].forEach(item => {
                    if (item.product.product && item.productReference) {
                        uniqueCodesSet.add(item.productReference)
                    }
                })
            }
            return Array.from(uniqueCodesSet)
        },
        // async addReferenceReference(payload){
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
            await api(`/api/references/${payload}`, 'DELETE')
            this.componentReference = this.componentReference.filter(retard => Number(retard['@id'].match(/\d+/)[0]) !== payload)
        },
        async fetch() {
            if (this.currentPage < 1){
                this.currentPage = 1
            }
            // const response = await api(`/api/references/componentFilter/${this.componentID}??page=${this.currentPage}`, 'GET')
            const response = await api(`/api/references/componentFilter/${this.componentID}?page=${this.currentPage}`, 'GET')
            this.componentReference = await this.updatePagination(response)
        },
        async filterBy(payload) {
            let url = `/api/references/componentFilter/${this.componentID}?`
            if (payload === ''){
                await this.fetch()
            } else {
                if (payload.name !== '') {
                    url += `name=${payload.name}&`
                }
                if (payload.kind !== '') {
                    url += `kind=${payload.kind}&`
                }
                if (payload.sampleQuantity !== '') {
                    url += `sampleQuantity=${payload.sampleQuantity}&`
                }
                if (payload.minValue !== ''){
                    if (payload.minValue.value !== '') {
                        url += `minValue=${payload.minValue.value}&`
                    }
                    if (payload.minValue.code !== '') {
                        url += `minCode=${payload.minValue.code}&`
                    }
                }
                if (payload.maxValue !== ''){
                    if (payload.maxValue.value !== '') {
                        url += `maxValue=${payload.maxValue.value}&`
                    }
                    if (payload.maxValue.code !== '') {
                        url += `maxCode=${payload.maxValue.code}&`
                    }
                }
                url += 'page=1'
                this.currentPage = 1
                const response = await api(url, 'GET')
                this.componentReference = await this.updatePagination(response)
            }
        },
        async itemsPagination(nPage) {
            const response = await api(`/api/references/componentFilter/${this.componentID}??page=${this.currentPage}&page=${nPage}`, 'GET')
            this.componentReference = await this.updatePagination(response)
        },
        async paginationSortableOrFilterItems(payload) {
            let response = {}
            if (payload.filter.value === true && payload.sortable.value === true){
                let url = `/api/references/componentFilter/${this.componentID}??page=${this.currentPage}&`
                if (payload.filterBy.value.name !== '') {
                    url += `name=${payload.filterBy.value.name}&`
                }
                if (payload.filterBy.value.kind !== '') {
                    url += `kind=${payload.filterBy.value.kind}&`
                }
                if (payload.filterBy.value.sampleQuantity !== '') {
                    url += `sampleQuantity=${payload.filterBy.value.sampleQuantity}&`
                }
                if (payload.filterBy.value.minValue !== ''){
                    if (payload.filterBy.value.minValue.value !== '') {
                        url += `minValue=${payload.filterBy.value.minValue.value}&`
                    }
                    if (payload.filterBy.value.minValue.code !== '') {
                        url += `minCode=${payload.filterBy.value.minValue.code}&`
                    }
                }
                if (payload.filterBy.value.maxValue !== ''){
                    if (payload.filterBy.value.maxValue.value !== '') {
                        url += `maxValue=${payload.filterBy.value.maxValue.value}&`
                    }
                    if (payload.filterBy.value.maxValue.code !== '') {
                        url += `maxCode=${payload.filterBy.value.maxValue.code}&`
                    }
                }

                response = await api(url, 'GET')
                this.componentReference = await this.updatePagination(response)
            } else if (payload.filter.value === true){
                let url = `/api/references/componentFilter/${this.componentID}??page=${this.currentPage}&`
                if (payload.filterBy.value.name !== '') {
                    url += `name=${payload.filterBy.value.name}&`
                }
                if (payload.filterBy.value.kind !== '') {
                    url += `kind=${payload.filterBy.value.kind}&`
                }
                if (payload.filterBy.value.sampleQuantity !== '') {
                    url += `sampleQuantity=${payload.filterBy.value.sampleQuantity}&`
                }
                if (payload.filterBy.value.minValue !== ''){
                    if (payload.filterBy.value.minValue.value !== '') {
                        url += `minValue=${payload.filterBy.value.minValue.value}&`
                    }
                    if (payload.filterBy.value.minValue.code !== '') {
                        url += `minCode=${payload.filterBy.value.minValue.code}&`
                    }
                }
                if (payload.filterBy.value.maxValue !== ''){
                    if (payload.filterBy.value.maxValue.value !== '') {
                        url += `maxValue=${payload.filterBy.value.maxValue.value}&`
                    }
                    if (payload.filterBy.value.maxValue.code !== '') {
                        url += `maxCode=${payload.filterBy.value.maxValue.code}&`
                    }
                }
                url += `page=${payload.nPage}`
                response = await api(url, 'GET')
                this.componentReference = await this.updatePagination(response)
            } else if (payload.sortable.value === false) {
                response = await api(`/api/references/componentFilter/${this.componentID}??page=${this.currentPage}&page=${payload.nPage}`, 'GET')
                this.componentReference = await this.updatePagination(response)
            } else {
                if (payload.trierAlpha.value.composant === 'component') {
                    response = await api(`/api/references/componentFilter/${this.componentID}??page=${this.currentPage}&order%5B${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                } else {
                    response = await api(`/api/references/componentFilter/${this.componentID}??page=${this.currentPage}&order%5Baddress.${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                }
                this.componentReference = await this.updatePagination(response)
            }
        },

        async sortableItems(payload, filterBy, filter) {
            let response = {}
            if (filter.value === true){
                if (filterBy.value.name !== '') {
                    url += `name=${filterBy.value.name}&`
                }
                if (filterBy.value.kind !== '') {
                    url += `kind=${filterBy.value.kind}&`
                }
                if (filterBy.value.sampleQuantity !== '') {
                    url += `sampleQuantity=${filterBy.value.sampleQuantity}&`
                }
                if (filterBy.value.minValue !== ''){
                    if (filterBy.value.minValue.value !== '') {
                        url += `minValue=${filterBy.value.minValue.value}&`
                    }
                    if (filterBy.value.minValue.code !== '') {
                        url += `minCode=${filterBy.value.minValue.code}&`
                    }
                }
                if (filterBy.value.maxValue !== ''){
                    if (filterBy.value.maxValue.value !== '') {
                        url += `maxValue=${filterBy.value.maxValue.value}&`
                    }
                    if (filterBy.value.maxValue.code !== '') {
                        url += `maxCode=${filterBy.value.maxValue.code}&`
                    }
                }
                url += `page=${this.currentPage}`
                response = await api(url, 'GET')
                this.componentReference = await this.updatePagination(response)
            } else {
                if (payload.composant === 'component') {
                    response = await api(`/api/references/componentFilter/${this.componentID}??page=${this.currentPage}&order%5B${payload.composant}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                } else {
                    response = await api(`/api/references/componentFilter/${this.componentID}??page=${this.currentPage}&order%5B${payload.produit}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                }
                this.componentReference = await this.updatePagination(response)
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
        itemsComponentReference: state => state.componentReference.map(item => {
            const newObject = {
                '@id': item['@id'],
                name: item.name,
                kind: item.kind,
                sampleQuantity: item.sampleQuantity,
                minValue: `${item.minValue} ${item.minCode}`,
                maxValue: `${item.maxValeur} ${item.maxCode}`
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
        componentReference: [],
        componentID: 0
    })
})
