import {defineStore} from 'pinia'
import api from '../../../api'

export default function useBalanceSheetItemStore(base = 'base') {
    const storeName = `balanceSheetItems/${base}`
    return defineStore(storeName, {
        actions: {
            async add(data) {
                await api('/api/balance-sheet-items', 'POST', data)
            },
            async fetch(criteria = '?pagination=false') {
                this.resetState()
                const response = await api(`/api/balance-sheet-items${criteria}`, 'GET')
                this.items = response['hydra:member']
            },
            async fetchById(id) {
                this.resetState()
                this.item = await api(`/api/balance-sheet-items/${id}`, 'GET')
            },
            async update(data, id) {
                await api(`/api/balance-sheet-items/${id}`, 'PATCH', data)
            },
            async remove(id) {
                //console.log('balanceSheetItems.remove', id)
                await api(`/api/balance-sheet-items/${id}`, 'DELETE')
            },
            resetState() {
                this.currentPage = ''
                this.firstPage = ''
                this.lastPage = ''
                this.nextPage = ''
                this.pagination = false
                this.previousPage = ''
                this.items = []
                this.item = null
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
