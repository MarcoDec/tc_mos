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
        setName(name){
            this.items.name = name
        },
        setCompany(comp){
            this.items.company = comp
        },
        setDestination(dest){
            this.items.destination = dest
        },
        setFamilies(tabFam){
            this.items.families = tabFam
        },
        dispose() {
            this.$reset()
            this.$dispose()
        },
        async remove() {
            await api(`/api/warehouses/${this.id}`, 'DELETE')
        },
        async update() {
            const data = {
                name: this.items.name,
                company: this.items.company,
                destination: this.items.destination,
                families: this.items.families
            }

            const response = await api(`/api/warehouses/${this.index}`, 'PATCH', data)
            this.$state = {...response}
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
