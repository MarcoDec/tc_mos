import api from '../../api'
import {defineStore} from 'pinia'

export const useIncotermStore = defineStore('incoterms', {
    actions: {
        async fetchOne() {
            const response = await api('/api/incoterms', 'GET')
            this.incoterms = response['hydra:member']
        }

    },
    getters: {
    },
    state: () => ({
        incoterms: []
    })
})
