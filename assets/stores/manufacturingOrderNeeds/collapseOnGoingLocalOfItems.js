import {defineStore} from 'pinia'
import api from './../../api'

export const useCollapseOnGoingLocalOfItemsStore = defineStore('collapseOnGoingLocalOfItems', {
    actions: {
        async fetchItems() {
            try {
                const response = await api('/api/collapseOnGoingLocalOfItems', 'GET')
                this.items = response
            } catch (error) {
                console.error(error)
            }
        }
    },
    getters: {
    },
    state: () => ({
        items: []
    })
})