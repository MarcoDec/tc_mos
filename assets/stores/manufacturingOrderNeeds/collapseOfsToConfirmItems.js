import {defineStore} from 'pinia'
import api from './../../api'

export const useCollapseOfsToConfirmItemsStore = defineStore('collapseOfsToConfirmItems', {
    actions: {
        async fetchItems() {
            try {
                const response = await api('/api/collapseOfsToConfirmItems', 'GET')
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