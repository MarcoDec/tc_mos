import api from '../../../../api'
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

                    if (payload.filterBy.value.creeLe !== '') {
                        url += `confirmedDate=${payload.filterBy.value.creeLe}&`
                    }
                    if (payload.filterBy.value.composant !== '') {
                        url += `item=${payload.filterBy.value.composant}&`
                    }

                    if (payload.filterBy.value.dateSouhaitee !== '') {
                        url += `requestedDate=${payload.filterBy.value.dateSouhaitee}&`
                    }

                    if (payload.filterBy.value.quantiteSouhaitee !== ''){
                        if (payload.filterBy.value.quantiteSouhaitee.value !== '') {
                            url += `requestedQuantityValue=${payload.filterBy.value.quantiteSouhaitee.value}&`
                        }
                        if (payload.filterBy.value.quantiteSouhaitee.code !== '') {
                            url += `requestedQuantityCode=${payload.filterBy.value.quantiteSouhaitee.code}&`
                        }
                    }

                    if (payload.filterBy.value.quantiteEffectuee !== ''){
                        if (payload.filterBy.value.quantiteEffectuee.value !== '') {
                            url += `confirmedQuantityValue=${payload.filterBy.value.quantiteEffectuee.value}&`
                        }
                        if (payload.filterBy.value.quantiteEffectuee.code !== '') {
                            url += `confirmedQuantityCode=${payload.filterBy.value.quantiteEffectuee.code}&`
                        }
                    }
                    if (payload.filterBy.value.note !== ''){
                        url += `note=${payload.filterBy.value.note}&`
                    }
                    if (payload.filterBy.value.retard !== ''){
                        url += `retard=${payload.filterBy.value.retard}&`
                    }
                    url += `page=${payload.nPage}`
                    response = await api(url, 'GET')
                    this.supplierFourniture = await this.updatePagination(response)
                } else {
                    let url = `api/purchase-order-items/supplierFilter/${this.supplierID}?`

                    if (payload.filterBy.value.creeLe !== '') {
                        url += `confirmedDate=${payload.filterBy.value.creeLe}&`
                    }
                    if (payload.filterBy.value.composant !== '') {
                        url += `item=${payload.filterBy.value.composant}&`
                    }

                    if (payload.filterBy.value.dateSouhaitee !== '') {
                        url += `requestedDate=${payload.filterBy.value.dateSouhaitee}&`
                    }

                    if (payload.filterBy.value.quantiteSouhaitee !== ''){
                        if (payload.filterBy.value.quantiteSouhaitee.value !== '') {
                            url += `requestedQuantityValue=${payload.filterBy.value.quantiteSouhaitee.value}&`
                        }
                        if (payload.filterBy.value.quantiteSouhaitee.code !== '') {
                            url += `requestedQuantityCode=${payload.filterBy.value.quantiteSouhaitee.code}&`
                        }
                    }

                    if (payload.filterBy.value.quantiteEffectuee !== ''){
                        if (payload.filterBy.value.quantiteEffectuee.value !== '') {
                            url += `confirmedQuantityValue=${payload.filterBy.value.quantiteEffectuee.value}&`
                        }
                        if (payload.filterBy.value.quantiteEffectuee.code !== '') {
                            url += `confirmedQuantityCode=${payload.filterBy.value.quantiteEffectuee.code}&`
                        }
                    }
                    if (payload.filterBy.value.note !== ''){
                        url += `note=${payload.filterBy.value.note}&`
                    }
                    if (payload.filterBy.value.retard !== ''){
                        url += `retard=${payload.filterBy.value.retard}&`
                    }
                    response = await api(url, 'GET')
                    this.supplierFourniture = await this.updatePagination(response)
                }
            } else if (payload.filter.value === true){
                let url = `api/purchase-order-items/supplierFilter/${this.supplierID}?`

                if (payload.filterBy.value.creeLe !== '') {
                    url += `confirmedDate=${payload.filterBy.value.creeLe}&`
                }
                if (payload.filterBy.value.composant !== '') {
                    url += `item=${payload.filterBy.value.composant}&`
                }

                if (payload.filterBy.value.dateSouhaitee !== '') {
                    url += `requestedDate=${payload.filterBy.value.dateSouhaitee}&`
                }

                if (payload.filterBy.value.quantiteSouhaitee !== ''){
                    if (payload.filterBy.value.quantiteSouhaitee.value !== '') {
                        url += `requestedQuantityValue=${payload.filterBy.value.quantiteSouhaitee.value}&`
                    }
                    if (payload.filterBy.value.quantiteSouhaitee.code !== '') {
                        url += `requestedQuantityCode=${payload.filterBy.value.quantiteSouhaitee.code}&`
                    }
                }

                if (payload.filterBy.value.quantiteEffectuee !== ''){
                    if (payload.filterBy.value.quantiteEffectuee.value !== '') {
                        url += `confirmedQuantityValue=${payload.filterBy.value.quantiteEffectuee.value}&`
                    }
                    if (payload.filterBy.value.quantiteEffectuee.code !== '') {
                        url += `confirmedQuantityCode=${payload.filterBy.value.quantiteEffectuee.code}&`
                    }
                }
                if (payload.filterBy.value.note !== ''){
                    url += `note=${payload.filterBy.value.note}&`
                }
                if (payload.filterBy.value.retard !== ''){
                    url += `retard=${payload.filterBy.value.retard}&`
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

                    if (filterBy.value.creeLe !== '') {
                        url += `confirmedDate=${filter.value.creeLe}&`
                    }
                    if (filterBy.value.composant !== '') {
                        url += `item=${filterBy.value.composant}&`
                    }

                    if (filterBy.value.dateSouhaitee !== '') {
                        url += `requestedDate=${filterBy.value.dateSouhaitee}&`
                    }

                    if (filterBy.value.quantiteSouhaitee !== ''){
                        if (filterBy.value.quantiteSouhaitee.value !== '') {
                            url += `requestedQuantityValue=${filterBy.value.quantiteSouhaitee.value}&`
                        }
                        if (filterBy.value.quantiteSouhaitee.code !== '') {
                            url += `requestedQuantityCode=${filterBy.value.quantiteSouhaitee.code}&`
                        }
                    }

                    if (filterBy.value.quantiteEffectuee !== ''){
                        if (filterBy.value.quantiteEffectuee.value !== '') {
                            url += `confirmedQuantityValue=${filterBy.value.quantiteEffectuee.value}&`
                        }
                        if (filterBy.value.quantiteEffectuee.code !== '') {
                            url += `confirmedQuantityCode=${filterBy.value.quantiteEffectuee.code}&`
                        }
                    }
                    if (filterBy.value.note !== ''){
                        url += `note=${filterBy.value.note}&`
                    }
                    if (filterBy.value.retard !== ''){
                        url += `retard=${filterBy.value.retard}&`
                    }

                    url += `page=${this.currentPage}`
                    response = await api(url, 'GET')
                } else {
                    let url = `api/purchase-order-items/supplierFilter/${this.supplierID}?`

                    if (filterBy.value.creeLe !== '') {
                        url += `confirmedDate=${filter.value.creeLe}&`
                    }
                    if (filterBy.value.composant !== '') {
                        url += `item=${filterBy.value.composant}&`
                    }

                    if (filterBy.value.dateSouhaitee !== '') {
                        url += `requestedDate=${filterBy.value.dateSouhaitee}&`
                    }

                    if (filterBy.value.quantiteSouhaitee !== ''){
                        if (filterBy.value.quantiteSouhaitee.value !== '') {
                            url += `requestedQuantityValue=${filterBy.value.quantiteSouhaitee.value}&`
                        }
                        if (filterBy.value.quantiteSouhaitee.code !== '') {
                            url += `requestedQuantityCode=${filterBy.value.quantiteSouhaitee.code}&`
                        }
                    }

                    if (filterBy.value.quantiteEffectuee !== ''){
                        if (filterBy.value.quantiteEffectuee.value !== '') {
                            url += `confirmedQuantityValue=${filterBy.value.quantiteEffectuee.value}&`
                        }
                        if (filterBy.value.quantiteEffectuee.code !== '') {
                            url += `confirmedQuantityCode=${filterBy.value.quantiteEffectuee.code}&`
                        }
                    }
                    if (filterBy.value.note !== ''){
                        url += `note=${filterBy.value.note}&`
                    }
                    if (filterBy.value.retard !== ''){
                        url += `retard=${filterBy.value.retard}&`
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
            // console.log(response)
            const responseData = await response['hydra:member']
            let paginationView = {}
            if (Object.prototype.hasOwnProperty.call(response, 'hydra:view')) {
                paginationView = response['hydra:view']
            } else {
                paginationView = responseData
            }
            // console.log(paginationView)
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
            // console.log(item)
            const newObject = {
                '@id': item['@id'],
                creeLe: item.confirmedDate,
                composant: item.item.code,
                prix: item.price,
                refFournisseur: item.ref,
                quantite: item.requestedQuantity,
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
            for (const supplier of response['hydra:member']) {
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
