import {defineStore} from 'pinia'

export const useProductionPlanningsItemsStore = defineStore('productionPlanningsItems', {
    actions: {
        fetchItems() {
            this.items = [

            ]
        }

    },
    getters: {
    },
    state: () => ({
        items: []
    })
})
