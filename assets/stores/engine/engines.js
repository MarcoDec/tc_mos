import api from '../../api'
import {defineStore} from 'pinia'

export const useEngineStore = defineStore('engines', {
    actions: {
        async fetch() {
            const response = await api('/api/engines/1', 'GET')
            this.engines = await response
            console.log('this.engines', this.engines)
        }

    },
    getters: {

    },
    state: () => ({
        engines: {}
    })
})
