import api from '../../api'
import {defineStore} from 'pinia'
import generateComponentAttribute from './componentAttribute'

export const useComponentShowStore = defineStore('componentAttributes', {
    actions: {
        async fetch() {
            this.items = []
            const response = await api('/api/component-attributes?component=1', 'GET')
            this.componentAttribute = await response['hydra:member']
            for (const component of response['hydra:member']) {
                const item = generateComponentAttribute(component, this)
                this.items.push(item)
            }
        },
        async update(data, id) {
            const response = await api('/api/component-attributes/'+ id, 'PATCH', data)
            this.fetch()
        }

    },
    getters: {

    },
    state: () => ({
        componentAttribute: []
    })
})
