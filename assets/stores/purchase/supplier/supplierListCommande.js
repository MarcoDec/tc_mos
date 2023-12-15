import api from '../../../api'
import {defineStore} from 'pinia'

export const useSupplierListCommandeStore = defineStore('supplierListCommande', {
    actions: {
        setIdSupplier(id){
            this.supplierID = id
        },
        // async addSupplierCommande(payload){
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
            await api(`/api/purchase-order-items/${payload}`, 'DELETE')
            this.supplierCommande = this.supplierCommande.filter(commande => Number(commande['@id'].match(/\d+/)[0]) !== payload)
        },
        async fetch() {
            // const response = await api(`/api/purchase-order-items?supplier=${this.supplierID}`, 'GET')
            if (this.currentPage < 1){
                this.currentPage = 1
            }
            // const response = await api(`/api/purchase-order-items/supplierFilter/${this.supplierID}?page=${this.currentPage}`, 'GET')
            const response = await api(`/api/purchase-order-items/supplierFilter/${this.supplierID}`, 'GET')
            this.supplierCommande = await this.updatePagination(response)
        },
        async filterBy(payload) {
            let url = `api/purchase-order-items/supplierFilter/${this.supplierID}?`
            if (payload === ''){
                await this.fetch()
            } else {
                if (payload.reference !== '') {
                    url += `refOrder=${payload.reference}&`
                }
                if (payload.statutFournisseur !== '') {
                    url += `statutFournisseur=${payload.statutFournisseur}&`
                }

                if (payload.supplementFret !== ''){
                    if (payload.supplementFret === true){
                        url += 'supplementFret=1&'
                    } else {
                        url += 'supplementFret=0&'
                    }
                }

                if (payload.infoPublic !== ''){
                    url += `infoPublic=${payload.infoPublic}&`
                }

                if (payload.commentaire !== ''){
                    url += `note=${payload.commentaire}&`
                }
                url += 'page=1'
                this.currentPage = 1
                const response = await api(url, 'GET')
                this.supplierCommande = await this.updatePagination(response)
            }
        },
        async itemsPagination(nPage) {
            const response = await api(`/api/purchase-order-items/supplierFilter/${this.supplierID}?page=${nPage}`, 'GET')
            this.supplierCommande = await this.updatePagination(response)
        },
        async paginationSortableOrFilterItems(payload) {
            let response = {}
            if (payload.filter.value === true && payload.sortable.value === true){
                if (payload.trierAlpha.value.component === 'component') {
                    let url = `api/purchase-order-items/supplierFilter/${this.supplierID}?`

                    if (payload.filterBy.value.reference !== '') {
                        url += `refOrder=${payload.filterBy.value.reference}&`
                    }
                    if (payload.filterBy.value.statutFournisseur !== '') {
                        url += `statutFournisseur=${payload.filterBy.value.statutFournisseur}&`
                    }

                    if (payload.filterBy.value.supplementFret !== '') {
                        if (payload.filterBy.value.supplementFret === true){
                            url += 'supplementFret=1&'
                        } else {
                            url += 'supplementFret=0&'
                        }
                    }
                    if (payload.filterBy.value.commentaire !== ''){
                        url += `note=${payload.filterBy.value.commentaire}&`
                    }
                    if (payload.filterBy.value.infoPublic !== ''){
                        url += `infoPublic=${payload.filterBy.value.infoPublic}&`
                    }
                    url += `page=${payload.nPage}`
                    response = await api(url, 'GET')
                    this.supplierCommande = await this.updatePagination(response)
                } else {
                    let url = `api/purchase-order-items/supplierFilter/${this.supplierID}?`

                    if (payload.filterBy.value.reference !== '') {
                        url += `refOrder=${payload.filterBy.value.reference}&`
                    }
                    if (payload.filterBy.value.composant !== '') {
                        url += `item=${payload.filterBy.value.composant}&`
                    }

                    if (payload.filterBy.value.statutFournisseur !== '') {
                        url += `statutFournisseur=${payload.filterBy.value.statutFournisseur}&`
                    }
                    if (payload.filterBy.value.supplementFret !== '') {
                        if (payload.filterBy.value.supplementFret === true){
                            url += 'supplementFret=1&'
                        } else {
                            url += 'supplementFret=0&'
                        }
                    }
                    if (payload.filterBy.value.infoPublic !== '') {
                        url += `infoPublic=${payload.filterBy.value.infoPublic}&`
                    }
                    if (payload.filterBy.value.commentaire !== ''){
                        url += `note=${payload.filterBy.value.commentaire}&`
                    }
                    response = await api(url, 'GET')
                    this.supplierCommande = await this.updatePagination(response)
                }
            } else if (payload.filter.value === true){
                let url = `api/purchase-order-items/supplierFilter/${this.supplierID}?`

                if (payload.filterBy.value.reference !== '') {
                    url += `refOrder=${payload.filterBy.value.reference}&`
                }
                if (payload.filterBy.value.composant !== '') {
                    url += `item=${payload.filterBy.value.composant}&`
                }
                if (payload.filterBy.value.statutFournisseur !== '') {
                    url += `statutFournisseur=${payload.filterBy.value.statutFournisseur}&`
                }
                if (payload.filterBy.value.supplementFret !== '') {
                    if (payload.filterBy.value.supplementFret === true){
                        url += 'supplementFret=1&'
                    } else {
                        url += 'supplementFret=0&'
                    }
                }
                if (payload.filterBy.value.infoPublic !== '') {
                    url += `infoPublic=${payload.filterBy.value.infoPublic}&`
                }
                if (payload.filterBy.value.commentaire !== ''){
                    url += `note=${payload.filterBy.value.commentaire}&`
                }
                url += `page=${payload.nPage}`
                response = await api(url, 'GET')
                this.supplierCommande = await this.updatePagination(response)
            } else if (payload.sortable.value === false) {
                response = await api(`/api/purchase-order-items/supplierFilter/${this.supplierID}?page=${payload.nPage}`, 'GET')
                this.supplierCommande = await this.updatePagination(response)
            } else {
                if (payload.trierAlpha.value.composant === 'component') {
                    response = await api(`/api/purchase-order-items/supplierFilter/${this.supplierID}?order%5B${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                } else {
                    response = await api(`/api/purchase-order-items/supplierFilter/${this.supplierID}?&order%5Baddress.${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                }
                this.supplierCommande = await this.updatePagination(response)
            }
        },

        async sortableItems(payload, filterBy, filter) {
            let response = {}
            if (filter.value === true){
                if (payload.composant === 'component') {
                    let url = `api/purchase-order-items/supplierFilter/${this.supplierID}?`

                    if (filterBy.value.reference !== '') {
                        url += `refOrder=${filterBy.value.reference}&`
                    }
                    if (filterBy.value.statutFournisseur !== '') {
                        url += `statutFournisseur=${filterBy.value.statutFournisseur}&`
                    }

                    if (filterBy.value.supplementFret !== '') {
                        if (filterBy.value.supplementFret === true){
                            url += 'supplementFret=1&'
                        } else {
                            url += 'supplementFret=0&'
                        }
                    }
                    if (filterBy.value.commentaire !== ''){
                        url += `note=${filterBy.value.commentaire}&`
                    }
                    if (filterBy.value.infoPublic !== ''){
                        url += `infoPublic=${filterBy.value.infoPublic}&`
                    }

                    url += `page=${this.currentPage}`
                    response = await api(url, 'GET')
                } else {
                    let url = `api/purchase-order-items/supplierFilter/${this.supplierID}?`

                    if (filterBy.value.reference !== '') {
                        url += `refOrder=${filter.value.reference}&`
                    }
                    if (filterBy.value.statutFournisseur !== '') {
                        url += `statutFournisseur=${filterBy.value.statutFournisseur}&`
                    }

                    if (filterBy.value.supplementFret !== '') {
                        if (filterBy.value.supplementFret === true){
                            url += 'supplementFret=1&'
                        } else {
                            url += 'supplementFret=0&'
                        }
                    }
                    if (filterBy.value.infoPublic !== ''){
                        url += `infoPublic=${filterBy.value.infoPublic}&`
                    }
                    if (filterBy.value.commentaire !== ''){
                        url += `note=${filterBy.value.commentaire}&`
                    }
                    url += `page=${this.currentPage}`
                    response = await api(url, 'GET')
                }
                this.supplierCommande = await this.updatePagination(response)
            } else {
                if (payload.composant === 'component') {
                    response = await api(`api/purchase-order-items/supplierFilter/${this.supplierID}?order%5B${payload.composant}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                } else {
                    response = await api(`api/purchase-order-items/supplierFilter/${this.supplierID}?order%5B${payload.produit}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                }
                this.supplierCommande = await this.updatePagination(response)
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
        }// ,
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
        itemsSupplierCommande: state => state.supplierCommande.map(item => {
            const newObject = {
                '@id': item['@id'],
                reference: item.order.ref,
                statutFournisseur: item.embState.state,
                supplementFret: item.order.supplementFret,
                commentaire: item.notes,
                infoPublic: item.order.notes
            }
            return newObject
        }),
        async getOptionEmbState() {
            const opt = []
            const codes = new Set()
            //todo changeer
            if (this.currentPage < 1){
                this.currentPage = 1
            }
            const response = await api(`/api/purchase-order-items/supplierFilter/${this.supplierID}?page=${this.currentPage}`, 'GET')

            for (const supplier of response['hydra:member'][2]) {
                if (!codes.has(supplier.embState.state)) {
                    opt.push({value: supplier.embState.state, text: supplier.embState.state, id: supplier.embState['@id']})
                    codes.add(supplier.embState.state)
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
        supplierCommande: [],
        supplierID: 0
    })
})
