import api from '../../api'
import {defineStore} from 'pinia'

const baseUrl = '/api/label-cartons'
export const useLabelCartonsStore = defineStore('labelCartons', {
    actions: {
        async deleted(payload) {
            await api(`${baseUrl}/${payload}`, 'DELETE')
        },
        async fetch() {
            if (this.currentPage < 1){
                this.currentPage = 1
            }
            const response = await api(`${baseUrl}/${this.labelCartonID}?`, 'GET')
            this.labelCartons = await this.updatePagination(response)
        },
        async filterBy(payload) {
            let url = `${baseUrl}/${this.labelCartonID}?`
            if (payload === ''){
                await this.fetch()
            } else {
                if (payload.date !== '') {
                    url += `date=${payload.date}&`
                }
                if (payload.type !== '') {
                    url += `type=${payload.type}&`
                }
                if (payload.name !== '') {
                    url += `name=${payload.name}&`
                }
                // if (payload.quantite !== '') {
                //     if (payload.quantite.code !== ''){
                //         url += `quantiteCode=${payload.quantite.code}&`
                //     }
                //     if (payload.quantite.value !== ''){
                //         url += `quantiteValue=${payload.quantite.value}&`
                //     }
                // }
                url += 'page=1'
                this.currentPage = 1
                const response = await api(url, 'GET')
                this.equipementPrevision = await this.updatePagination(response)
            }
        },
        async itemsPagination(nPage) {
            const response = await api(`${baseUrl}/${this.labelCartonID}?page=${nPage}`, 'GET')
            this.equipementPrevision = await this.updatePagination(response)
        },
        async paginationSortableOrFilterItems(payload) {
            let response = {}
            if (payload.filter.value === true && payload.sortable.value === true){
                let url = `${baseUrl}/${this.labelCartonID}?`
                if (payload.filterBy.value.date !== '') {
                    url += `date=${payload.filterBy.value.date}&`
                }
                if (payload.filterBy.value.type !== '') {
                    url += `type=${payload.filterBy.value.type}&`
                }
                if (payload.filterBy.value.name !== '') {
                    url += `name=${payload.filterBy.value.name}&`
                }
                if (payload.filterBy.value.quantite !== '') {
                    if (payload.filterBy.value.quantite.code !== ''){
                        url += `quantiteCode=${payload.filterBy.value.quantite.code}&`
                    }
                    if (payload.filterBy.value.quantite.value !== ''){
                        url += `quantiteValue=${payload.filterBy.value.quantite.value}&`
                    }
                }
                response = await api(url, 'GET')
                this.equipementPrevision = await this.updatePagination(response)
            } else if (payload.filter.value === true){
                let url = `${baseUrl}/${this.labelCartonID}?`
                if (payload.filterBy.value.date !== '') {
                    url += `date=${payload.filterBy.value.date}&`
                }
                if (payload.filterBy.value.type !== '') {
                    url += `type=${payload.filterBy.value.type}&`
                }
                if (payload.filterBy.value.name !== '') {
                    url += `name=${payload.filterBy.value.name}&`
                }
                if (payload.filterBy.value.quantite !== '') {
                    if (payload.filterBy.value.quantite.code !== ''){
                        url += `quantiteCode=${payload.filterBy.value.quantite.code}&`
                    }
                    if (payload.filterBy.value.quantite.value !== ''){
                        url += `quantiteValue=${payload.filterBy.value.quantite.value}&`
                    }
                }
                url += `page=${payload.nPage}`
                response = await api(url, 'GET')
                this.equipementPrevision = await this.updatePagination(response)
            } else if (payload.sortable.value === false) {
                response = await api(`${baseUrl}/${this.labelCartonID}?page=${payload.nPage}`, 'GET')
                this.equipementPrevision = await this.updatePagination(response)
            } else {
                if (payload.trierAlpha.value.composant === 'equipement') {
                    response = await api(`${baseUrl}/${this.labelCartonID}?order%5B${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                } else {
                    response = await api(`${baseUrl}/${this.labelCartonID}?order%5Baddress.${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                }
                this.equipementPrevision = await this.updatePagination(response)
            }
        },

        async sortableItems(payload, filterBy, filter) {
            let response = {}
            if (filter.value === true){
                if (filterBy.value.date !== '') {
                    url += `date=${filterBy.value.date}&`
                }
                if (filterBy.value.type !== '') {
                    url += `type=${filterBy.value.type}&`
                }
                if (filterBy.value.name !== '') {
                    url += `name=${filterBy.value.name}&`
                }
                if (filterBy.value.quantite !== '') {
                    if (filterBy.value.quantite.code !== ''){
                        url += `quantiteCode=${filterBy.value.quantite.code}&`
                    }
                    if (filterBy.value.quantite.value !== ''){
                        url += `quantiteValue=${filterBy.value.quantite.value}&`
                    }
                }
                url += `page=${this.currentPage}`
                response = await api(url, 'GET')
                this.equipementPrevision = await this.updatePagination(response)
            } else {
                if (payload.composant === 'equipement') {
                    response = await api(`${baseUrl}/${this.labelCartonID}?order%5B${payload.composant}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                } else {
                    response = await api(`${baseUrl}/${this.labelCartonID}?order%5B${payload.produit}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                }
                this.equipementPrevision = await this.updatePagination(response)
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
    },
    state: () => ({
        currentPage: '',
        firstPage: '',
        lastPage: '',
        nextPage: '',
        pagination: false,
        previousPage: '',
        labelCarton: null,
        labelCartons: [],
        labelCartonID: 0
    })
})
