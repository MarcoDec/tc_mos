import api from '../../../../api'
import {defineStore} from 'pinia'

export const useProductListCommandeStore = defineStore('productListCommande', {
    actions: {
        setIdProduct(id){
            this.productID = id
        },
        // async addProductCommande(payload){
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
            await api(`/api/selling-order-items/${payload}`, 'DELETE')
            this.productCommande = this.productCommande.filter(commande => Number(commande['@id'].match(/\d+/)[0]) !== payload)
        },
        async fetch() {
            // const response = await api(`/api/selling-order-items?product=${this.productID}`, 'GET')
            if (this.currentPage < 1){
                this.currentPage = 1
            }
            // const response = await api(`/api/selling-order-items/productFilter/${this.productID}?page=${this.currentPage}`, 'GET')
            const response = await api('/api/selling-order-items/', 'GET')
            this.productCommande = await this.updatePagination(response)
        },
        async filterBy(payload) {
            let url = `api/selling-order-items/productFilter/${this.productID}?`
            if (payload === ''){
                await this.fetch()
            } else {
                if (payload.creeLe !== '') {
                    url += `confirmedDate=${payload.creeLe}&`
                }
                if (payload.composant !== '') {
                    url += `item=${payload.composant}&`
                }

                if (payload.dateSouhaitee !== '') {
                    url += `requestedDate=${payload.dateSouhaitee}&`
                }

                if (payload.quantiteSouhaitee !== ''){
                    if (payload.quantiteSouhaitee.value !== '') {
                        url += `requestedQuantityValue=${payload.quantiteSouhaitee.value}&`
                    }
                    if (payload.quantiteSouhaitee.code !== '') {
                        url += `requestedQuantityCode=${payload.quantiteSouhaitee.code}&`
                    }
                }

                if (payload.quantiteEffectuee !== ''){
                    if (payload.quantiteEffectuee.value !== '') {
                        url += `confirmedQuantityValue=${payload.quantiteEffectuee.value}&`
                    }
                    if (payload.quantiteEffectuee.code !== '') {
                        url += `confirmedQuantityCode=${payload.quantiteEffectuee.code}&`
                    }
                }
                if (payload.note !== ''){
                    url += `note=${payload.note}&`
                }

                if (payload.commande !== ''){
                    url += `commande=${payload.commande}&`
                }
                url += 'page=1'
                this.currentPage = 1
                const response = await api(url, 'GET')
                this.productCommande = await this.updatePagination(response)
            }
        },
        async itemsPagination(nPage) {
            const response = await api(`/api/selling-order-items/productFilter/${this.productID}?page=${nPage}`, 'GET')
            this.productCommande = await this.updatePagination(response)
        },
        async paginationSortableOrFilterItems(payload) {
            let response = {}
            if (payload.filter.value === true && payload.sortable.value === true){
                if (payload.trierAlpha.value.component === 'component') {
                    let url = `api/selling-order-items/productFilter/${this.productID}?`

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
                    if (payload.filterBy.value.commande !== ''){
                        url += `commande=${payload.filterBy.value.commande}&`
                    }
                    url += `page=${payload.nPage}`
                    response = await api(url, 'GET')
                    this.productCommande = await this.updatePagination(response)
                } else {
                    let url = `api/selling-order-items/productFilter/${this.productID}?`

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
                    if (payload.filterBy.value.commande !== ''){
                        url += `commande=${payload.filterBy.value.commande}&`
                    }
                    response = await api(url, 'GET')
                    this.productCommande = await this.updatePagination(response)
                }
            } else if (payload.filter.value === true){
                let url = `api/selling-order-items/productFilter/${this.productID}?`

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
                if (payload.filterBy.value.commande !== ''){
                    url += `commande=${payload.filterBy.value.commande}&`
                }
                url += `page=${payload.nPage}`
                response = await api(url, 'GET')
                this.productCommande = await this.updatePagination(response)
            } else if (payload.sortable.value === false) {
                response = await api(`/api/selling-order-items/productFilter/${this.productID}?page=${payload.nPage}`, 'GET')
                this.productCommande = await this.updatePagination(response)
            } else {
                if (payload.trierAlpha.value.composant === 'component') {
                    response = await api(`/api/selling-order-items/productFilter/${this.productID}?order%5B${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                } else {
                    response = await api(`/api/selling-order-items/productFilter/${this.productID}?&order%5Baddress.${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                }
                this.productCommande = await this.updatePagination(response)
            }
        },

        async sortableItems(payload, filterBy, filter) {
            let response = {}
            if (filter.value === true){
                if (payload.composant === 'component') {
                    let url = `api/selling-order-items/productFilter/${this.productID}?`

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
                    if (filterBy.value.commande !== ''){
                        url += `commande=${filterBy.value.commande}&`
                    }

                    url += `page=${this.currentPage}`
                    response = await api(url, 'GET')
                } else {
                    let url = `api/selling-order-items/productFilter/${this.productID}?`

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
                    if (filterBy.value.commande !== ''){
                        url += `commande=${filterBy.value.commande}&`
                    }
                    url += `page=${this.currentPage}`
                    response = await api(url, 'GET')
                }
                this.productCommande = await this.updatePagination(response)
            } else {
                if (payload.composant === 'component') {
                    response = await api(`api/selling-order-items/productFilter/${this.productID}?order%5B${payload.composant}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                } else {
                    response = await api(`api/selling-order-items/productFilter/${this.productID}?order%5B${payload.produit}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                }
                this.productCommande = await this.updatePagination(response)
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
            //console.log(paginationView)
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
        itemsProductCommande: state => state.productCommande.map(item => {
            console.log(item)
            let retrd = 'Aucun'
            const quantiteEff = item.confirmedQuantity
            const quantiteSou = item.requestedQuantity
            const dateSou = item.requestedDate

            const currentDate = new Date()
            const dateSouSplit = dateSou.split('-')
            const yearSouhaitee = parseInt(dateSouSplit[0].trim())
            const monthSouhaitee = parseInt(dateSouSplit[1].trim())
            const daySouhaitee = parseInt(dateSouSplit[2].trim())
            const souhaiteeDate = new Date(yearSouhaitee, monthSouhaitee - 1, daySouhaitee)

            if (quantiteSou.value > quantiteEff.value && currentDate > souhaiteeDate){
                let years = currentDate.getFullYear() - souhaiteeDate.getFullYear()
                let months = currentDate.getMonth() - souhaiteeDate.getMonth()
                let days = currentDate.getDate() - souhaiteeDate.getDate()

                if (months < 0 || months === 0 && days < 0) {
                    years--
                    if (currentDate.getMonth() < souhaiteeDate.getMonth()) {
                        months += 12
                    } else if (currentDate.getMonth() === souhaiteeDate.getMonth()) {
                        if (currentDate.getDate() < souhaiteeDate.getDate()) {
                            months--
                        }
                    }
                }

                if (days < 0) {
                    const lastMonthDate = new Date(
                        currentDate.getFullYear(),
                        currentDate.getMonth() - 1,
                        0
                    )
                    days += lastMonthDate.getDate()
                    months--
                }

                retrd = ''
                if (years !== 0){
                    retrd += `${years}y `
                }
                if (months !== 0){
                    retrd += `${months}m `
                }
                if (days !== 0){
                    retrd += `${days}d `
                }
            }
            const newObject = {
                '@id': item['@id'],
                creeLe: item.confirmedDate,
                composant: item.item.code,
                commande: retrd,
                evenement: '',
                message: '',
                dateSouhaitee: dateSou,
                note: item.notes,
                fournisseurFerme: '',
                composantFournisseur: '',
                quantiteSouhaitee: quantiteSou,
                quantiteEffectuee: quantiteEff
            }
            return newObject
        }),
        async getOptionComposant() {
            const opt = []
            const codes = new Set()
            //todo changeer
            if (this.currentPage < 1){
                this.currentPage = 1
            }
            const response = await api(`/api/selling-order-items/productFilter/${this.productID}?page=${this.currentPage}`, 'GET')
            for (const product of response['hydra:member']) {
                if (!codes.has(product.item.code)) {
                    opt.push({value: product.item['@id'], '@type': product.item['@type'], text: product.item.code, id: product.item.id})
                    codes.add(product.item.code)
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
        productCommande: [],
        productID: 0
    })
})
