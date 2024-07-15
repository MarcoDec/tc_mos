import {defineStore} from 'pinia'
import api from './../../api'

export const useCollapseNewOfsItemsStore = defineStore('collapseNewOfsItems', {
    actions: {
        async fetchItems(companyId) {
            try {
                const response = await api('/api/collapseNewOfsItems', 'GET', {companyId})
                this.items = response
                this.page = response.page
                this.limit = response.limit
                this.totalPages = response.totalPages
            } catch (error) {
                console.error(error)
            }
        }
    },
    getters: {
    },
    state: () => ({
        items: [],
        page: 1,
        limit: 10,
        totalPages: 0
    })
})
