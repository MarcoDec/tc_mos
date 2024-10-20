import api from '../../../api'
import {defineStore} from 'pinia'

export const useSupplierListRetardStore = defineStore('supplierListRetard', {
    actions: {
        setIdSupplier(id){
            this.supplierID = id
        },
        // async addSupplierRetard(payload){
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
            this.supplierRetard = this.supplierRetard.filter(retard => Number(retard['@id'].match(/\d+/)[0]) !== payload)
        },
        async fetch() {
            // const response = await api(`/api/purchase-order-items?supplier=${this.supplierID}`, 'GET')
            if (this.currentPage < 1){
                this.currentPage = 1
            }
            // const response = await api(`/api/purchase-order-items/supplierFilter/${this.supplierID}?page=${this.currentPage}`, 'GET')
            const response = await api(`/api/purchase-order-items/supplierFilter/${this.supplierID}`, 'GET')
            this.supplierRetard = await this.updatePagination(response)
        },
        async filterBy(payload) {
            let url = `api/purchase-order-items/supplierFilter/${this.supplierID}?`
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

                if (payload.retard !== ''){
                    url += `retard=${payload.retard}&`
                }
                url += 'page=1'
                this.currentPage = 1
                const response = await api(url, 'GET')
                this.supplierRetard = await this.updatePagination(response)
            }
        },
        async itemsPagination(nPage) {
            const response = await api(`/api/purchase-order-items/supplierFilter/${this.supplierID}?page=${nPage}`, 'GET')
            this.supplierRetard = await this.updatePagination(response)
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
                    this.supplierRetard = await this.updatePagination(response)
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
                    this.supplierRetard = await this.updatePagination(response)
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
                this.supplierRetard = await this.updatePagination(response)
            } else if (payload.sortable.value === false) {
                response = await api(`/api/purchase-order-items/supplierFilter/${this.supplierID}?page=${payload.nPage}`, 'GET')
                this.supplierRetard = await this.updatePagination(response)
            } else {
                if (payload.trierAlpha.value.composant === 'component') {
                    response = await api(`/api/purchase-order-items/supplierFilter/${this.supplierID}?order%5B${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                } else {
                    response = await api(`/api/purchase-order-items/supplierFilter/${this.supplierID}?&order%5Baddress.${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                }
                this.supplierRetard = await this.updatePagination(response)
            }
        },

        async sortableItems(payload, filterBy, filter) {
            let response = {}
            if (filter.value === true){
                if (payload.composant === 'component') {
                    let url = `api/purchase-order-items/supplierFilter/${this.supplierID}?`

                    if (filterBy.value.creeLe !== '') {
                        url += `confirmedDate=${filterBy.value.creeLe}&`
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
                this.supplierRetard = await this.updatePagination(response)
            } else {
                if (payload.composant === 'component') {
                    response = await api(`api/purchase-order-items/supplierFilter/${this.supplierID}?order%5B${payload.composant}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                } else {
                    response = await api(`api/purchase-order-items/supplierFilter/${this.supplierID}?order%5B${payload.produit}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                }
                this.supplierRetard = await this.updatePagination(response)
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
        itemsSupplierRetard: state => state.supplierRetard.map(item => {
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
                retard: retrd,
                evenement: '',
                message: '',
                dateSouhaitee: dateSou,
                note: item.notes,
                fournisseurFerme: '',
                composantFournisseur: '',
                quantiteSouhaitee: `${quantiteSou.value} ${quantiteSou.code}`,
                quantiteEffectuee: `${quantiteEff.value} ${quantiteEff.code}`
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
        supplierRetard: [],
        supplierID: 0
    })
})
