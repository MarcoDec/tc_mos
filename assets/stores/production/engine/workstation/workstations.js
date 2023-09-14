import api from '../../../../api'
import {defineStore} from 'pinia'

export const useWorkstationsStore = defineStore('workstations', {
    actions: {
        async create(data) {
            const response = await api('/api/workstations', 'POST', data)
            this.engine = await response
            this.isLoaded = true
        },
        async fetchAll() {
            const response = await api('/api/workstations', 'GET')
            this.engines = await response
            this.isLoaded = true
        },
        async fetchOne(id) {
            const response = await api(`/api/workstations/${id}`, 'GET')
            this.engine = await response
            this.isLoaded = true
        },
        async remove(id) {
            const response = await api(`/api/workstations/${id}`, 'DELETE')
            this.engine = await response
            this.isLoaded = true
        },
        async update(data) {
            const response = await api(`/api/workstations/${this.engine.id}`, 'PATCH', data)
            this.engine = await response
        }
    },
    getters: {

    },
    state: () => ({
        engine: {},
        engines: [],
        isLoaded: false
    })
})
