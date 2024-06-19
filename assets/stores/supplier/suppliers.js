import {actionsItems, gettersItems} from '../tables/items'
import api from '../../api'
import {defineStore} from 'pinia'

export default defineStore('suppliers', {
    actions: {
        ...actionsItems,
        // async fetch() {
        //     const response = [
        //         {
        //             etat: 'refus',
        //             id: 1,
        //             nom: '3M FRANCE'

        //         },
        //         {
        //             etat: 'attente',
        //             id: 2,
        //             nom: 'ABB'

        //         },
        //         {
        //             etat: 'valide',
        //             id: 3,
        //             nom: 'CD'

        //         }
        //     ]
        //     for (const supplier of response)
        //         this.items.push(generateSupplier(supplier, this))
        // }
        async fetch() {
            const response = await api('/api/suppliers', 'GET')
            //console.log('response', response)
            this.suppliers = await this.updatePagination(response)
            // console.log('this.suppliers', this.suppliers)
        },
        async delated(payload){
            await api(`/api/suppliers/${payload}`, 'DELETE')
            this.suppliers = this.suppliers.filter(supplier => Number(supplier['@id'].match(/\d+/)[0]) !== payload)
        },
        async itemsPagination(nPage) {
            // console.log('nPage', nPage)
            const response = await api(`/api/suppliers?page=${nPage}`, 'GET')
            // console.log('response', response)
            this.suppliers = await this.updatePagination(response)
        },
        async updatePagination(response) {
            const responseData = await response['hydra:member']
            const paginationView = response['hydra:view']
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

    },
    getters: {
        ...gettersItems,
        label() {
            return value =>
                this.options.find(option => option.value === value)?.text ?? null
        },
        options: state =>
            state.items
                .map(supplier => supplier.option)
                .sort((a, b) => a.text.localeCompare(b.text))
    },

    state: () => ({
        suppliers: [],
        currentPage: '',
        firstPage: '',
        lastPage: '',
        nextPage: '',
        pagination: false,
        previousPage: ''
    })
})
