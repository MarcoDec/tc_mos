import api from '../../api'
import {defineStore} from 'pinia'
import generateComponentAttribute from './componentAttribute'

export const useComponentAttributesStore = defineStore('componentAttributes', {
    actions: {
        async fetchByComponentId(id = 1) {
            this.componentAttribute = []
            const response = await api(`/api/component-attributes?component=${id}`, 'GET')
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
