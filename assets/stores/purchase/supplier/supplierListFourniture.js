import api from '../../api'
import {defineStore} from 'pinia'

export const useSupplierListFournitureStore = defineStore('supplierListFourniture', {
    actions: {
        setIdSupplier(id){
            this.supplierID = id
        },
        async addSupplierFourniture(payload){
            const violations = []
            try {
                if (payload.quantite.value !== ''){
                    payload.quantite.value = parseInt(payload.quantite.value)
                }
                const element = {
                    component: payload.composant,
                    refFournisseur: payload.refFournisseur,
                    prix: payload.prix,
                    quantity: payload.quantite,
                    texte: payload.texte
                }
                await api('/api/component-stocks', 'POST', element)
                this.fetch()
            } catch (error) {
                violations.push({message: error})
            }
            return violations
        },
        async deleted(payload) {
            await api(`/api/purchase-order-items/${payload}`, 'DELETE')
            this.supplierFourniture = this.supplierFourniture.filter(retard => Number(retard['@id'].match(/\d+/)[0]) !== payload)
        },
        async fetch() {
            // const response = await api(`/api/purchase-order-items?supplier=${this.supplierID}`, 'GET')
            if (this.currentPage < 1){
                this.currentPage = 1
            }
            // const response = await api(`/api/purchase-order-items/supplierFilter/${this.supplierID}?page=${this.currentPage}`, 'GET')
            const response = await api(`/api/purchase-order-items/supplierFilter/${this.supplierID}`, 'GET')
            this.supplierFourniture = await this.updatePagination(response)
        },
        async filterBy(payload) {
            let url = `api/purchase-order-items/supplierFilter/${this.supplierID}?`
            if (payload === ''){
                await this.fetch()
            } else {
                if (payload.composant !== '') {
                    url += `item=${payload.composant}&`
                }

                if (payload.quantite !== ''){
                    if (payload.quantite.value !== '') {
                        url += `requestedQuantityValue=${payload.quantite.value}&`
                    }
                    if (payload.quantite.code !== '') {
                        url += `requestedQuantityCode=${payload.quantite.code}&`
                    }
                }

                if (payload.prix !== ''){
                    if (payload.prix.value !== '') {
                        url += `prixValue=${payload.prix.value}&`
                    }
                    if (payload.prix.code !== '') {
                        url += `prixCode=${payload.prix.code}&`
                    }
                }

                if (payload.texte !== ''){
                    url += `note=${payload.texte}&`
                }

                if (payload.refFournisseur !== ''){
                    url += `ref=${payload.refFournisseur}&`
                }
                url += 'page=1'
                this.currentPage = 1
                const response = await api(url, 'GET')
                this.supplierFourniture = await this.updatePagination(response)
            }
        },
        async itemsPagination(nPage) {
            const response = await api(`/api/purchase-order-items/supplierFilter/${this.supplierID}?page=${nPage}`, 'GET')
            this.supplierFourniture = await this.updatePagination(response)
        },
        async paginationSortableOrFilterItems(payload) {
            let response = {}
            if (payload.filter.value === true && payload.sortable.value === true){
                if (payload.trierAlpha.value.component === 'component') {
                    let url = `api/purchase-order-items/supplierFilter/${this.supplierID}?`

                    if (payload.filterBy.value.composant !== '') {
                        url += `item=${payload.filterBy.value.composant}&`
                    }

                    if (payload.filterBy.value.quantite !== ''){
                        if (payload.filterBy.value.quantite.value !== '') {
                            url += `requestedQuantityValue=${payload.filterBy.value.quantite.value}&`
                        }
                        if (payload.filterBy.value.quantite.code !== '') {
                            url += `requestedQuantityCode=${payload.filterBy.value.quantite.code}&`
                        }
                    }

                    if (payload.filterBy.value.prix !== ''){
                        if (payload.filterBy.value.prix.value !== '') {
                            url += `prixValue=${payload.filterBy.value.prix.value}&`
                        }
                        if (payload.filterBy.value.prix.code !== '') {
                            url += `prixCode=${payload.filterBy.value.prix.code}&`
                        }
                    }

                    if (payload.filterBy.value.texte !== ''){
                        url += `note=${payload.filterBy.value.texte}&`
                    }
                    if (payload.filterBy.value.refFournisseur !== ''){
                        url += `ref=${payload.filterBy.value.refFournisseur}&`
                    }

                    url += `page=${payload.nPage}`

                    response = await api(url, 'GET')
                    this.supplierFourniture = await this.updatePagination(response)
                } else {
                    let url = `api/purchase-order-items/supplierFilter/${this.supplierID}?`

                    if (payload.filterBy.value.composant !== '') {
                        url += `item=${payload.filterBy.value.composant}&`
                    }

                    if (payload.filterBy.value.quantite !== ''){
                        if (payload.filterBy.value.quantite.value !== '') {
                            url += `requestedQuantityValue=${payload.filterBy.value.quantite.value}&`
                        }
                        if (payload.filterBy.value.quantite.code !== '') {
                            url += `requestedQuantityCode=${payload.filterBy.value.quantite.code}&`
                        }
                    }

                    if (payload.filterBy.value.prix !== ''){
                        if (payload.filterBy.value.prix.value !== '') {
                            url += `prixValue=${payload.filterBy.value.prix.value}&`
                        }
                        if (payload.filterBy.value.prix.code !== '') {
                            url += `prixCode=${payload.filterBy.value.prix.code}&`
                        }
                    }

                    if (payload.filterBy.value.texte !== ''){
                        url += `note=${payload.filterBy.value.texte}&`
                    }
                    if (payload.filterBy.value.refFournisseur !== ''){
                        url += `ref=${payload.filterBy.value.refFournisseur}&`
                    }
                    response = await api(url, 'GET')

                    this.supplierFourniture = await this.updatePagination(response)
                }
            } else if (payload.filter.value === true){
                let url = `api/purchase-order-items/supplierFilter/${this.supplierID}?`

                if (payload.filterBy.value.composant !== '') {
                    url += `item=${payload.filterBy.value.composant}&`
                }

                if (payload.filterBy.value.quantite !== ''){
                    if (payload.filterBy.value.quantite.value !== '') {
                        url += `requestedQuantityValue=${payload.filterBy.value.quantite.value}&`
                    }
                    if (payload.filterBy.value.quantite.code !== '') {
                        url += `requestedQuantityCode=${payload.filterBy.value.quantite.code}&`
                    }
                }

                if (payload.filterBy.value.prix !== ''){
                    if (payload.filterBy.value.prix.value !== '') {
                        url += `prixValue=${payload.filterBy.value.prix.value}&`
                    }
                    if (payload.filterBy.value.prix.code !== '') {
                        url += `prixCode=${payload.filterBy.value.prix.code}&`
                    }
                }

                if (payload.filterBy.value.texte !== ''){
                    url += `note=${payload.filterBy.value.texte}&`
                }
                if (payload.filterBy.value.refFournisseur !== ''){
                    url += `ref=${payload.filterBy.value.refFournisseur}&`
                }
                url += `page=${payload.nPage}`
                response = await api(url, 'GET')
                this.supplierFourniture = await this.updatePagination(response)
            } else if (payload.sortable.value === false) {
                response = await api(`/api/purchase-order-items/supplierFilter/${this.supplierID}?page=${payload.nPage}`, 'GET')
                this.supplierFourniture = await this.updatePagination(response)
            } else {
                if (payload.trierAlpha.value.composant === 'component') {
                    response = await api(`/api/purchase-order-items/supplierFilter/${this.supplierID}?order%5B${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                } else {
                    response = await api(`/api/purchase-order-items/supplierFilter/${this.supplierID}?&order%5Baddress.${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                }
                this.supplierFourniture = await this.updatePagination(response)
            }
        },

        async sortableItems(payload, filterBy, filter) {
            let response = {}
            if (filter.value === true){
                if (payload.composant === 'component') {
                    let url = `api/purchase-order-items/supplierFilter/${this.supplierID}?`

                    if (filterBy.value.composant !== '') {
                        url += `item=${filterBy.value.composant}&`
                    }

                    if (filterBy.value.quantite !== ''){
                        if (filterBy.value.quantite.value !== '') {
                            url += `requestedQuantityValue=${filterBy.value.quantite.value}&`
                        }
                        if (filterBy.value.quantite.code !== '') {
                            url += `requestedQuantityCode=${filterBy.value.quantite.code}&`
                        }
                    }

                    if (filterBy.value.prix !== ''){
                        if (filterBy.value.prix.value !== '') {
                            url += `prixValue=${filterBy.value.prix.value}&`
                        }
                        if (filterBy.value.prix.code !== '') {
                            url += `prixCode=${filterBy.value.prix.code}&`
                        }
                    }

                    if (filterBy.value.texte !== ''){
                        url += `note=${filterBy.value.texte}&`
                    }
                    if (filterBy.value.refFournisseur !== ''){
                        url += `ref=${filterBy.value.refFournisseur}&`
                    }

                    url += `page=${this.currentPage}`
                    response = await api(url, 'GET')
                } else {
                    let url = `api/purchase-order-items/supplierFilter/${this.supplierID}?`

                    if (filterBy.value.composant !== '') {
                        url += `item=${filterBy.value.composant}&`
                    }

                    if (ilterBy.value.quantite !== ''){
                        if (filterBy.value.quantite.value !== '') {
                            url += `requestedQuantityValue=${filterBy.value.quantite.value}&`
                        }
                        if (filterBy.value.quantite.code !== '') {
                            url += `requestedQuantityCode=${filterBy.value.quantite.code}&`
                        }
                    }

                    if (filterBy.value.prix !== ''){
                        if (filterBy.value.prix.value !== '') {
                            url += `prixValue=${filterBy.value.prix.value}&`
                        }
                        if (filterBy.value.prix.code !== '') {
                            url += `prixCode=${filterBy.value.prix.code}&`
                        }
                    }

                    if (filterBy.value.texte !== ''){
                        url += `note=${filterBy.value.texte}&`
                    }
                    if (filterBy.value.refFournisseur !== ''){
                        url += `ref=${filterBy.value.refFournisseur}&`
                    }
                    url += `page=${this.currentPage}`
                    response = await api(url, 'GET')
                }
                this.supplierFourniture = await this.updatePagination(response)
            } else {
                if (payload.composant === 'component') {
                    response = await api(`api/purchase-order-items/supplierFilter/${this.supplierID}?order%5B${payload.composant}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                } else {
                    response = await api(`api/purchase-order-items/supplierFilter/${this.supplierID}?order%5B${payload.produit}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                }
                this.supplierFourniture = await this.updatePagination(response)
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
        },
        async updateWarehouseStock(payload){
            await api(`/api/stocks/${payload.id}`, 'PATCH', payload.itemsUpdateData)
            if (payload.sortable.value === true || payload.filter.value === true) {
                this.paginationSortableOrFilterItems({filter: payload.filter, filterBy: payload.filterBy, nPage: this.currentPage, sortable: payload.sortable, trierAlpha: payload.trierAlpha})
            } else {
                this.itemsPagination(this.currentPage)
            }
            this.fetch()
        }
    },
    getters: {
        itemsSupplierFourniture: state => state.supplierFourniture.map(item => {
            const newObject = {
                '@id': item['@id'],
                creeLe: item.confirmedDate,
                composant: item.item.code,
                prix: `${item.price.value} ${item.price.code}`,
                refFournisseur: item.ref,
                quantite: `${item.requestedQuantity.value} ${item.requestedQuantity.code}`,
                texte: item.notes
            }
            return newObject
        }),
        async getOptionComposant() {
            const opt = []
            const codes = new Set()
            //todo changer
            if (this.currentPage < 1){
                this.currentPage = 1
            }
            const response = await api(`/api/purchase-order-items/supplierFilter/${this.supplierID}?page=${this.currentPage}`, 'GET')
            for (const supplier of response['hydra:member'][2]) {
                if (!codes.has(supplier.item.code)) {
                    opt.push({value: supplier.item['@id'], '@type': supplier.item['@type'], text: supplier.item.code, id: supplier.item.id})
                    codes.add(supplier.item.code)
                }
            }
            opt.sort((a, b) => {
                const textA = a.text.toLowerCase()
                const textB = b.text.toLowerCase()
                if (textA < textB) {
                    return -1
                }
                if (textA > textB) {
                    return 1
                }
                return 0
            })

            return opt.length === 0 ? [{text: 'Aucun élément'}] : opt
        }
    },
    state: () => ({
        currentPage: '',
        firstPage: '',
        lastPage: '',
        nextPage: '',
        pagination: false,
        previousPage: '',
        supplierFourniture: [],
        supplierID: 0
    })
})
