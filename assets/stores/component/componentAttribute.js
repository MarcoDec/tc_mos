import api from '../../api'
import {defineStore} from 'pinia'

export default defineStore('componentAttributes', {
    actions: {
      
        async getComponentAttributes() {
            const response = await api('/api/component-attributes', 'GET')
            // console.log('responseComponentAttributes',response)
            this.listComponentAttribute = response['hydra:member']
        },
        async addComponentAttributes(payload){
            // console.log('payload', payload)
            this.componentAttributes = await api('/api/component-attributes', 'POST', payload)
            // console.log('respanse', this.componentAttributes);
        }
    },
    getters: {
    },
    state: () => ({
        listComponentAttributes: [],
        componentAttributes: []
    })
})
