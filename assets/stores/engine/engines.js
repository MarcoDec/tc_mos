import api from '../../api'
import {defineStore} from 'pinia'

export const useEngineStore = defineStore('engines', {
    actions: {
        async fetchOne() {
            const response = await api('/api/engines/1', 'GET')
            this.engines = await response
        }

    },
    getters: {

    },
    state: () => ({
        engines: {}
    })
})
