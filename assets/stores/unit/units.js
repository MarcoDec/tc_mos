import {defineStore} from 'pinia'
import api from '../../api'

export const useUnitsStore = defineStore('units', {
    actions: {
        async fetch() {
            const response = await api('/api/units', 'GET')
            this.units = response['hydra:member']
        }
    },
    getters: {},

    state: () => ({
        units: []
    })
})