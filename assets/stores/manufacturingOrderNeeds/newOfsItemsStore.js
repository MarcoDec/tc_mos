import {defineStore} from 'pinia'
import api from './../../api'

export const useCollapseNewOfsItemsStore = defineStore('collapseNewOfsItems', {
    actions: {
        async fetchItems(companyId) {
            try {
                const response = await api('/api/collapseNewOfsItems', 'GET', {companyId})
                this.items = response
                //à chaque item on ajoute une propriété 'id' qui est l'index de l'item dans le tableau
                this.items.forEach((item, index) => item.id = index)
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
