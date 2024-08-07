import {defineStore} from 'pinia'
import api from './../../api'

export const useCollapseOnGoingLocalOfItemsStore = defineStore('collapseOnGoingLocalOfItems', {
    actions: {
        async fetchItems(companyId) {
            try {
                const response = await api(`/api/collapseOnGoingLocalOfItems`, 'GET', {companyId})
                this.items = response
            } catch (error) {
                console.error(error)
            }
        }
    },
    getters: {},
    state: () => ({
        items: []
    })
})
