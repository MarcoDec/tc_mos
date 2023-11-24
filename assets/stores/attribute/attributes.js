import api from '../../api'
import {defineStore} from 'pinia'
import useAttribute from './attribute'

export default defineStore('attributes', {
    actions: {
        dispose() {
            for (const attribute of this.attributes)
                attribute.dispose()
            this.attributes = []
            this.isLoaded = false
            this.$dispose()
        },
        async fetch() {
            const response = await api(this.url)
            for (const attribute of response['hydra:member'])
                this.attributes.push(useAttribute(attribute, this))
            this.isLoaded = true
        },
        removeAttribute(removed) {
            this.attributes = this.attributes.filter(attribute => attribute['@id'] !== removed['@id'])
        },
        update(attributes, family) {
            for (const attribute of this.attributes)
                attribute.update(attributes, family)
        }
    },
    getters: {
        fields: state => state.attributes.map(attribute => attribute.field),
        modelValue: state => family => {
            const modelValue = {}
            for (const attribute of state.attributes)
                modelValue[attribute['@id']] = attribute.includes(family)
            return modelValue
        },
        url() {
            return `/api/${this.$id}?pagination=false`
        }
    },
    state: () => ({
        attributes: [],
        isLoaded: false
    })
})
