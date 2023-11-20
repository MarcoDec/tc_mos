import api from '../../../api'
import {defineStore} from 'pinia'
import generateComponentAttribute from './componentAttribute'

export const useComponentAttributesStore = defineStore('componentAttributes', {
    actions: {
        async fetchByComponentId(id = 1) {
            this.componentAttributes = []
            const response = await api(`/api/component-attributes?component=${id}`, 'GET')
            for (const attribute of response['hydra:member']) {
                const item = generateComponentAttribute(attribute, this)
                this.componentAttributes.push(item)
            }
        },
        async updateComponentAttributeColor(iri, newValue) {
            this.item = {}
            this.item = await api(iri, 'PATCH', {
                color: newValue
            })
        },
        async updateComponentAttributeMeasure(iri, newValue) {
            this.item = {}
            this.item = await api(iri, 'PATCH', {
                measure: {
                    code: newValue.code,
                    denominator: newValue.denominator,
                    value: Number(newValue.value)
                }
            })
        },
        async updateComponentAttributeValue(iri, newValue) {
            this.item = {}
            this.item = await api(iri, 'PATCH', {
                value: newValue
            })
        }
    },
    getters: {

    },
    state: () => ({
        componentAttributes: [],
        item: {},
        items: []
    })
})