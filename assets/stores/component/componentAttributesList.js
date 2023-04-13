import api from '../../api'
import {defineStore} from 'pinia'
import generateComponentAttribute from './componentAttribute'

export const useComponentShowStore = defineStore('componentAttributes', {
    actions: {
        async fetch() {
            this.componentAttribute = []
            const response = await api('/api/component-attributes?component=1', 'GET')
            for (const attribute of response['hydra:member']) {
                const item = generateComponentAttribute(attribute, this)
                this.componentAttribute.push(item)
            }
        }

    },
    getters: {

    },
    state: () => ({
        componentAttribute: [],
        items: []

    })
})
