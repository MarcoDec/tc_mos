import {defineStore} from 'pinia'
import api from '../../../api'

export const useBalanceSheetStore = defineStore('balanceSheetsStore', {
    actions: {
        async fetch(criteria = '?pagination=false') {
            const response = await api(`/api/balance-sheets${criteria}`, 'GET')
            this.items = response['hydra:member']
        },
        async fetchById(id) {
            this.item = await api(`/api/balance-sheets/${id}`, 'GET')
        },
        async update(data, id) {
            await api(`/api/balance-sheets/${id}`, 'PATCH', data)
        },
        async remove(id) {
            await api(`/api/balance-sheets/${id}`, 'DELETE')
        }
    },
    getters: {
    },
    state: () => ({
        currentPage: '',
        firstPage: '',
        lastPage: '',
        nextPage: '',
        pagination: false,
        previousPage: '',
        items: [],
        item: null
    })
})
