import api from '../../api'
import {defineStore} from 'pinia'
import generateComponentAttribute from './componentAttribute'

export const useComponentShowStore = defineStore('componentAttributes', {
    actions: {
        async fetch() {
            this.items = []
            const response = await api('/api/component-attributes?component=1', 'GET')
            console.log('response', response)
            this.componentAttribute = await response['hydra:member']
            for (const component of response['hydra:member']) {
                const item = generateComponentAttribute(component, this)
                this.items.push(item)
            }
        },
        async update(data) {
            console.log('je suis ici')
            const response = await api('/api/component-attributes/1', 'PATCH', data)
            console.log('response update', response)
            // /this.componentAttribute = await response['hydra:member']
        }

    },
    getters: {

    },
    state: () => ({
        componentAttribute: []
    })
})
