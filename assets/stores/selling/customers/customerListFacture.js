import api from '../../../api'
import {defineStore} from 'pinia'

export const useCustomerListFactureStore = defineStore('customerListFacture', {
    actions: {
        setIdCustomer(id){
            this.customerID = id
        },
        // async addFacture(payload){
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
            await api(`/api/bills/${payload}`, 'DELETE')
            this.customerFacture = this.customerFacture.filter(retard => Number(retard['@id'].match(/\d+/)[0]) !== payload)
        },
        async fetch() {
            // const response = await api(`/api/selling-order-items?customer=${this.customerID}`, 'GET')
            if (this.currentPage < 1){
                this.currentPage = 1
            }
            // const response = await api(`/api/selling-order-items/customerFilter/${this.customerID}?page=${this.currentPage}`, 'GET')
            // const response = await api(`/api/bills?customer=/api/customers/${this.customerID}&order%5Bnote.bill.dueDate%5D=desc`, 'GET')
            const response = await api(`/api/bills?customer=/api/customers/${this.customerID}`, 'GET')
            this.customerFacture = await this.updatePagination(response)
        },
        async filterBy(payload) {
            let url = `/api/bills?customer=/api/customers/${this.customerID}&order%5BdueDate%5D=desc&`
            if (payload === ''){
                await this.fetch()
            } else {
                if (payload.ref !== '') {
                    url += `ref=${payload.ref}&`
                }

                if (payload.dateFacture !== '') {
                    url += `billingDate=${payload.dateFacture}&`
                }
                if (payload.dateEcheance !== '') {
                    url += `dueDate=${payload.dateEcheance}&`
                }
                if (payload.forceTVA !== '') {
                    url += `forceVat=${payload.forceTVA}&`
                }
                if (payload.note !== '') {
                    url += `notes=${payload.note}&`
                }
                if (payload.msgTVA !== '') {
                    url += `vatMessage.name=${payload.msgTVA}&`
                }

                if (payload.prixHT !== ''){
                    if (payload.prixHT.value !== '') {
                        url += `exclTax.value=${payload.prixHT.value}&`
                    }
                    if (payload.prixHT.code !== '') {
                        url += `exclTax.code=${payload.prixHT.code}&`
                    }
                }

                if (payload.prixTTC !== ''){
                    if (payload.prixTTC.value !== '') {
                        url += `inclTax.value=${payload.prixTTC.value}&`
                    }
                    if (payload.prixTTC.code !== '') {
                        url += `inclTax.code=${payload.prixTTC.code}&`
                    }
                }
                if (payload.tva !== ''){
                    if (payload.tva.value !== '') {
                        url += `vat.value=${payload.tva.value}&`
                    }
                    if (payload.tva.code !== '') {
                        url += `vats.code=${payload.tva.code}&`
                    }
                }

                url += 'page=1'
                this.currentPage = 1
                const response = await api(url, 'GET')
                this.customerFacture = await this.updatePagination(response)
            }
        },
        async itemsPagination(nPage) {
            const response = await api(`/api/bills?customer=/api/customers/${this.customerID}&order%5BdueDate%5D=desc&page=${nPage}`, 'GET')
            this.customerFacture = await this.updatePagination(response)
        },
        async paginationSortableOrFilterItems(payload) {
            let response = {}
            if (payload.filter.value === true && payload.sortable.value === true){
                let url = `/api/bills?customer=/api/customers/${this.customerID}&order%5BdueDate%5D=desc&`
                if (payload.filterBy.value.ref !== '') {
                    url += `ref=${payload.filterBy.value.ref}&`
                }

                if (payload.filterBy.value.dateFacture !== '') {
                    url += `billingDate=${payload.filterBy.value.dateFacture}&`
                }
                if (payload.filterBy.value.dateEcheance !== '') {
                    url += `dueDate=${payload.filterBy.value.dateEcheance}&`
                }
                if (payload.filterBy.value.forceTVA !== '') {
                    url += `forceVat=${payload.filterBy.value.forceTVA}&`
                }
                if (payload.filterBy.value.note !== '') {
                    url += `notes=${payload.filterBy.value.note}&`
                }
                if (payload.filterBy.value.msgTVA !== '') {
                    url += `vatMessage.name=${payload.filterBy.value.msgTVA}&`
                }

                if (payload.filterBy.value.prixHT !== ''){
                    if (payload.filterBy.value.prixHT.value !== '') {
                        url += `exclTax.value=${payload.filterBy.value.prixHT.value}&`
                    }
                    if (payload.filterBy.value.prixHT.code !== '') {
                        url += `exclTax.code=${payload.filterBy.value.prixHT.code}&`
                    }
                }

                if (payload.filterBy.value.prixTTC !== ''){
                    if (payload.filterBy.value.prixTTC.value !== '') {
                        url += `inclTax.value=${payload.filterBy.value.prixTTC.value}&`
                    }
                    if (payload.filterBy.value.prixTTC.code !== '') {
                        url += `inclTax.code=${payload.filterBy.value.prixTTC.code}&`
                    }
                }
                if (payload.filterBy.value.tva !== ''){
                    if (payload.filterBy.value.tva.value !== '') {
                        url += `vat.value=${payload.filterBy.value.tva.value}&`
                    }
                    if (payload.filterBy.value.tva.code !== '') {
                        url += `vats.code=${payload.filterBy.value.tva.code}&`
                    }
                }

                response = await api(url, 'GET')
                this.customerFacture = await this.updatePagination(response)
            } else if (payload.filter.value === true){
                let url = `/api/bills?customer=/api/customers/${this.customerID}&order%5BdueDate%5D=desc&`
                if (payload.filterBy.value.ref !== '') {
                    url += `ref=${payload.filterBy.value.ref}&`
                }

                if (payload.filterBy.value.dateFacture !== '') {
                    url += `billingDate=${payload.filterBy.value.dateFacture}&`
                }
                if (payload.filterBy.value.dateEcheance !== '') {
                    url += `dueDate=${payload.filterBy.value.dateEcheance}&`
                }
                if (payload.filterBy.value.forceTVA !== '') {
                    url += `forceVat=${payload.filterBy.value.forceTVA}&`
                }
                if (payload.filterBy.value.note !== '') {
                    url += `notes=${payload.filterBy.value.note}&`
                }
                if (payload.filterBy.value.msgTVA !== '') {
                    url += `vatMessage.name=${payload.filterBy.value.msgTVA}&`
                }

                if (payload.filterBy.value.prixHT !== ''){
                    if (payload.filterBy.value.prixHT.value !== '') {
                        url += `exclTax.value=${payload.filterBy.value.prixHT.value}&`
                    }
                    if (payload.filterBy.value.prixHT.code !== '') {
                        url += `exclTax.code=${payload.filterBy.value.prixHT.code}&`
                    }
                }

                if (payload.filterBy.value.prixTTC !== ''){
                    if (payload.filterBy.value.prixTTC.value !== '') {
                        url += `inclTax.value=${payload.filterBy.value.prixTTC.value}&`
                    }
                    if (payload.filterBy.value.prixTTC.code !== '') {
                        url += `inclTax.code=${payload.filterBy.value.prixTTC.code}&`
                    }
                }
                if (payload.filterBy.value.tva !== ''){
                    if (payload.filterBy.value.tva.value !== '') {
                        url += `vat.value=${payload.filterBy.value.tva.value}&`
                    }
                    if (payload.filterBy.value.tva.code !== '') {
                        url += `vats.code=${payload.filterBy.value.tva.code}&`
                    }
                }

                url += `page=${payload.nPage}`
                response = await api(url, 'GET')
                this.customerFacture = await this.updatePagination(response)
            } else if (payload.sortable.value === false) {
                response = await api(`/api/bills?customer=/api/customers/${this.customerID}&order%5BdueDate%5D=desc&page=${payload.nPage}`, 'GET')
                this.customerFacture = await this.updatePagination(response)
            } else {
                if (payload.trierAlpha.value.composant === 'component') {
                    response = await api(`/api/bills?customer=/api/customers/${this.customerID}&order%5BdueDate%5D=desc&order%5B${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                } else {
                    response = await api(`/api/bills?customer=/api/customers/${this.customerID}&order%5BdueDate%5D=desc&order%5Baddress.${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                }
                this.customerFacture = await this.updatePagination(response)
            }
        },

        async sortableItems(payload, filterBy, filter) {
            let response = {}
            if (filter.value === true){
                if (filterBy.value.ref !== '') {
                    url += `ref=${filterBy.value.ref}&`
                }

                if (filterBy.value.dateFacture !== '') {
                    url += `billingDate=${filterBy.value.dateFacture}&`
                }
                if (filterBy.value.dateEcheance !== '') {
                    url += `dueDate=${filterBy.value.dateEcheance}&`
                }
                if (filterBy.value.forceTVA !== '') {
                    url += `forceVat=${filterBy.value.forceTVA}&`
                }
                if (filterBy.value.note !== '') {
                    url += `notes=${filterBy.value.note}&`
                }
                if (filterBy.value.msgTVA !== '') {
                    url += `vatMessage.name=${filterBy.value.msgTVA}&`
                }

                if (filterBy.value.prixHT !== ''){
                    if (filterBy.value.prixHT.value !== '') {
                        url += `exclTax.value=${filterBy.value.prixHT.value}&`
                    }
                    if (filterBy.value.prixHT.code !== '') {
                        url += `exclTax.code=${filterBy.value.prixHT.code}&`
                    }
                }

                if (filterBy.value.prixTTC !== ''){
                    if (filterBy.value.prixTTC.value !== '') {
                        url += `inclTax.value=${filterBy.value.prixTTC.value}&`
                    }
                    if (filterBy.value.prixTTC.code !== '') {
                        url += `inclTax.code=${filterBy.value.prixTTC.code}&`
                    }
                }
                if (filterBy.value.tva !== ''){
                    if (filterBy.value.tva.value !== '') {
                        url += `vat.value=${filterBy.value.tva.value}&`
                    }
                    if (filterBy.value.tva.code !== '') {
                        url += `vats.code=${filterBy.value.tva.code}&`
                    }
                }

                url += `page=${this.currentPage}`
                response = await api(url, 'GET')
                this.customerFacture = await this.updatePagination(response)
            } else {
                if (payload.composant === 'component') {
                    response = await api(`/api/bills?customer=/api/customers/${this.customerID}&order%5BdueDate%5D=desc&order%5B${payload.composant}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                } else {
                    response = await api(`/api/bills?customer=/api/customers/${this.customerID}&order%5BdueDate%5D=desc&order%5B${payload.produit}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                }
                this.customerFacture = await this.updatePagination(response)
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
        itemsCustomerFacture: state => state.customerFacture.map(item => {
            const dtFacture = item.billingDate.split('T')[0]
            const dtEcheance = item.dueDate.split('T')[0]
            const newObject = {
                '@id': item['@id'],
                ref: item.ref,
                dateFacture: dtFacture,
                dateEcheance: dtEcheance,
                forceTVA: item.forceVat,
                note: item.notes,
                msgTVA: item.vatMessage === null ? null : item.vatMessage.name,
                prixHT: `${item.exclTax.value} ${item.exclTax.code}`,
                prixTTC: `${item.inclTax.value} ${item.inclTax.code}`,
                tva: `${item.vat.value} ${item.vat.code}`
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
        customerFacture: [],
        customerID: 0
    })
})
