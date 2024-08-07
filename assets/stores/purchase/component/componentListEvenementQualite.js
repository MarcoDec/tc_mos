import api from '../../../api'
import {defineStore} from 'pinia'

export const useComponentListEvenementQualiteStore = defineStore('componentListEvenementQualite', {
    actions: {
        setIdComponent(id){
            this.componentID = id
        },
        getUniqueProductCodes(response) {
            const uniqueCodesSet = new Set()
            if (response && response['hydra:member']) {
                response['hydra:member'].forEach(item => {
                    if (item.product.product && item.productEvenementQualite) {
                        uniqueCodesSet.add(item.productEvenementQualite)
                    }
                })
            }
            return Array.from(uniqueCodesSet)
        },
        // async addEvenementQualiteEvenementQualite(payload){
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
            await api(`/api/production-qualities/${payload}`, 'DELETE')
            this.componentEvenementQualite = this.componentEvenementQualite.filter(retard => Number(retard['@id'].match(/\d+/)[0]) !== payload)
        },
        async fetch() {
            if (this.currentPage < 1){
                this.currentPage = 1
            }
            const response = await api(`/api/production-quality/componentFilter/${this.componentID}?`)
            this.componentEvenementQualite = await this.updatePagination(response)
        },
        async filterBy(payload) {
            let url = `/api/production-quality/componentFilter/${this.componentID}?`
            if (payload === ''){
                await this.fetch()
            } else {
                if (payload.creeLe !== '') {
                    url += `creeLe=${payload.creeLe}&`
                }
                if (payload.detectePar !== '') {
                    url += `detectePar=${payload.detectePar}&`
                }
                if (payload.ref !== '') {
                    url += `ref=${payload.ref}&`
                }
                if (payload.description !== '') {
                    url += `description=${payload.description}&`
                }
                if (payload.responsable !== '') {
                    url += `responsable=${payload.responsable}&`
                }
                if (payload.localisation !== '') {
                    url += `localisation=${payload.localisation}&`
                }
                if (payload.societe !== '') {
                    url += `societe=${payload.societe}&`
                }
                if (payload.progression !== ''){
                    if (payload.progression.value !== '') {
                        url += `progressionValue=${payload.progression.value}&`
                    }
                    if (payload.progression.code !== '') {
                        url += `progressionCode=${payload.progression.code}&`
                    }
                }
                if (payload.statut !== '') {
                    url += `statut=${payload.statut}&`
                }
                url += 'page=1'
                this.currentPage = 1
                const response = await api(url, 'GET')
                this.componentEvenementQualite = await this.updatePagination(response)
            }
        },
        async itemsPagination(nPage) {
            const response = await api(`/api/production-quality/componentFilter/${this.componentID}?page=${this.currentPage}&page=${nPage}`, 'GET')
            this.componentEvenementQualite = await this.updatePagination(response)
        },
        async paginationSortableOrFilterItems(payload) {
            let response = {}
            if (payload.filter.value === true && payload.sortable.value === true){
                let url = `/api/production-quality/componentFilter/${this.componentID}?page=${this.currentPage}&`
                if (payload.filterBy.value.creeLe !== '') {
                    url += `creeLe=${payload.filterBy.value.creeLe}&`
                }
                if (payload.filterBy.value.detectePar !== '') {
                    url += `detectePar=${payload.filterBy.value.detectePar}&`
                }
                if (payload.filterBy.value.ref !== '') {
                    url += `ref=${payload.filterBy.value.ref}&`
                }
                if (payload.filterBy.value.description !== '') {
                    url += `description=${payload.filterBy.value.description}&`
                }
                if (payload.filterBy.value.responsable !== '') {
                    url += `responsable=${payload.filterBy.value.responsable}&`
                }
                if (payload.filterBy.value.localisation !== '') {
                    url += `localisation=${payload.filterBy.value.localisation}&`
                }
                if (payload.filterBy.value.societe !== '') {
                    url += `societe=${payload.filterBy.value.societe}&`
                }
                if (payload.filterBy.value.progression !== ''){
                    if (payload.filterBy.value.progression.value !== '') {
                        url += `progressionValue=${payload.filterBy.value.progression.value}&`
                    }
                    if (payload.filterBy.value.progression.code !== '') {
                        url += `progressionCode=${payload.filterBy.value.progression.code}&`
                    }
                }
                if (payload.filterBy.value.statut !== '') {
                    url += `statut=${payload.filterBy.value.statut}&`
                }

                response = await api(url, 'GET')
                this.componentEvenementQualite = await this.updatePagination(response)
            } else if (payload.filter.value === true){
                let url = `/api/production-quality/componentFilter/${this.componentID}?page=${this.currentPage}&`
                if (payload.filterBy.value.creeLe !== '') {
                    url += `creeLe=${payload.filterBy.value.creeLe}&`
                }
                if (payload.filterBy.value.detectePar !== '') {
                    url += `detectePar=${payload.filterBy.value.detectePar}&`
                }
                if (payload.filterBy.value.ref !== '') {
                    url += `ref=${payload.filterBy.value.ref}&`
                }
                if (payload.filterBy.value.description !== '') {
                    url += `description=${payload.filterBy.value.description}&`
                }
                if (payload.filterBy.value.responsable !== '') {
                    url += `responsable=${payload.filterBy.value.responsable}&`
                }
                if (payload.filterBy.value.localisation !== '') {
                    url += `localisation=${payload.filterBy.value.localisation}&`
                }
                if (payload.filterBy.value.societe !== '') {
                    url += `societe=${payload.filterBy.value.societe}&`
                }
                if (payload.filterBy.value.progression !== ''){
                    if (payload.filterBy.value.progression.value !== '') {
                        url += `progressionValue=${payload.filterBy.value.progression.value}&`
                    }
                    if (payload.filterBy.value.progression.code !== '') {
                        url += `progressionCode=${payload.filterBy.value.progression.code}&`
                    }
                }
                if (payload.filterBy.value.statut !== '') {
                    url += `statut=${payload.filterBy.value.statut}&`
                }
                url += `page=${payload.nPage}`
                response = await api(url, 'GET')
                this.componentEvenementQualite = await this.updatePagination(response)
            } else if (payload.sortable.value === false) {
                response = await api(`/api/production-quality/componentFilter/${this.componentID}?page=${this.currentPage}&page=${payload.nPage}`, 'GET')
                this.componentEvenementQualite = await this.updatePagination(response)
            } else {
                if (payload.trierAlpha.value.composant === 'component') {
                    response = await api(`/api/production-quality/componentFilter/${this.componentID}?page=${this.currentPage}&order%5B${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                } else {
                    response = await api(`/api/production-quality/componentFilter/${this.componentID}?page=${this.currentPage}&order%5Baddress.${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                }
                this.componentEvenementQualite = await this.updatePagination(response)
            }
        },

        async sortableItems(payload, filterBy, filter) {
            let response = {}
            if (filter.value === true){
                if (filterBy.value.creeLe !== '') {
                    url += `creeLe=${filterBy.value.creeLe}&`
                }
                if (filterBy.value.detectePar !== '') {
                    url += `detectePar=${filterBy.value.detectePar}&`
                }
                if (filterBy.value.ref !== '') {
                    url += `ref=${filterBy.value.ref}&`
                }
                if (filterBy.value.description !== '') {
                    url += `description=${filterBy.value.description}&`
                }
                if (filterBy.value.responsable !== '') {
                    url += `responsable=${filterBy.value.responsable}&`
                }
                if (filterBy.value.localisation !== '') {
                    url += `localisation=${filterBy.value.localisation}&`
                }
                if (filterBy.value.societe !== '') {
                    url += `societe=${filterBy.value.societe}&`
                }
                if (filterBy.value.progression !== ''){
                    if (filterBy.value.progression.value !== '') {
                        url += `progressionValue=${filterBy.value.progression.value}&`
                    }
                    if (filterBy.value.progression.code !== '') {
                        url += `progressionCode=${filterBy.value.progression.code}&`
                    }
                }
                if (filterBy.value.statut !== '') {
                    url += `statut=${filterBy.value.statut}&`
                }
                url += `page=${this.currentPage}`
                response = await api(url, 'GET')
                this.componentEvenementQualite = await this.updatePagination(response)
            } else {
                if (payload.composant === 'component') {
                    response = await api(`/api/production-quality/componentFilter/${this.componentID}?page=${this.currentPage}&order%5B${payload.composant}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                } else {
                    response = await api(`/api/production-quality/componentFilter/${this.componentID}?page=${this.currentPage}&order%5B${payload.produit}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                }
                this.componentEvenementQualite = await this.updatePagination(response)
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
        //     await api(`/api/stocks/${payload.id}`, 'PATCH', payload.itemsUpcreeLeData)
        //     if (payload.sortable.value === true || payload.filter.value === true) {
        //         this.paginationSortableOrFilterItems({filter: payload.filter, filterBy: payload.filterBy, nPage: this.currentPage, sortable: payload.sortable, trierAlpha: payload.trierAlpha})
        //     } else {
        //         this.itemsPagination(this.currentPage)
        //     }
        //     this.fetch()
        // }
    },
    getters: {
        itemsComponentEvenementQualite: state => state.componentEvenementQualite.map(item => {
            const dtCreeLe = item.creeLe.split(' ')[0]
            const newObject = {
                '@id': `${item['@id']}`,
                creeLe: dtCreeLe,
                description: item.description,
                detectePar: `${item.detecteParName} ${item.detecteParSurname}`,
                localisation: `${item.localisation}`,
                progression: `${item.progressionValue} ${item.progressionCode}`,
                ref: item.ref,
                responsable: `${item.responsableName} ${item.responsableSurname}`,
                societe: item.societe,
                statut: item.statut
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
        componentEvenementQualite: [],
        componentID: 0
    })
})
