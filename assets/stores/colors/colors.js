import api from '../../api'
import {defineStore} from 'pinia'

export const useColorsStore = defineStore('colors', {
    actions: {
        async fetch() {
            const response = await api('/api/colors', 'GET')
            console.log('response color***===', response['hydra:member'])
            this.colors = await response['hydra:member']
        }
    },
    getters: {

    },
    state: () => ({
        colors: []
    })
})
