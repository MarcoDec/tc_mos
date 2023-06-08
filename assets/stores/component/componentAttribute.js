import api from '../../api'
import {defineStore} from 'pinia'

export const useComponentAttributeStore = defineStore('componentAttributes', {
    actions: {
      
        async getComponentAttributes() {
            const response = await api('/api/component-attributes', 'GET')
            console.log('responseComponentAttributes',response)
            this.listComponentAttribute = response['hydra:member']
        }
    },
    getters: {
    },
    state: () => ({
        listComponentAttributes: []
    })
})
