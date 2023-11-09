import {defineStore} from 'pinia'
import api from '../../../api'

export default function useBalanceSheetItemStore(base = 'base') {
    const id = `balanceSheetItems/${base}`
    return defineStore(id, {
        actions: {
            async add(data) {
                await api('/api/balance-sheet-items', 'POST', data)
            },
            async fetch(criteria = '?pagination=false') {
                const response = await api(`/api/balance-sheet-items${criteria}`, 'GET')
                this.items = response['hydra:member']
            },
            async fetchById(id) {
                this.item = await api(`/api/balance-sheet-items/${id}`, 'GET')
            },
            async update(data, id) {
                await api(`/api/balance-sheet-items/${id}`, 'PATCH', data)
            },
            async remove(id) {
                await api(`/api/balance-sheet-items/${id}`, 'DELETE')
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
    })()
}
