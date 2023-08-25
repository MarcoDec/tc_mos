import api from '../../api'
import {defineStore} from 'pinia'

export default defineStore('componentAttributes', {
    actions: {

        async getComponentAttributes() {
            const response = await api('/api/component-attributes', 'GET')
            this.listComponentAttribute = response['hydra:member']
        },
        async addComponentAttributes(payload){
            this.componentAttributes = await api('/api/component-attributes', 'POST', payload)
        }
    },
    getters: {
    },
    state: () => ({
        listComponentAttributes: [],
        componentAttributes: []
    })
})
