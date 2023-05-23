import api from '../../../api'
import {defineStore} from 'pinia'

export const useWarehouseShowStore = defineStore('warehouseShow', {
    state: () => ({
        items: [],
        index: null
    }),
    actions: {
        async fetch() {
            this.items = []
            const response = await api(`/api/warehouses/${this.index}`, 'GET')

            this.items = response
        },

        setCurrentId(id){
            sessionStorage.setItem('warehouseId', id)
            this.index = id
        },

        setFamilies(tabFam){
            this.items.families = tabFam
        }
    },
    getters: {
        getCurrentId(){
            if (this.index > 0 && sessionStorage.getItem('warehouseId') > 0) {
                this.index = sessionStorage.getItem('warehouseId')
                return this.index
            }
            return null
        },
        getNameWarehouse(){
            return this.items.name
        },
        getFamilies(){
            return this.items.families
        }
    }
})
