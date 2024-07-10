import {defineStore} from 'pinia'
import api from './../../api'

export const useProductionPlanningsItemsStore = defineStore('productionPlanningsItems', {
    actions: {
        async fetchItems() {
            try {
                const response = await api('/api/manufacturingSchedule', 'GET')
                console.log('Réponse de l\'API Items :', response.items)
                this.items = response.items
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
