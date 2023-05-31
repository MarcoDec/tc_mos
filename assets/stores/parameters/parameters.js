import api from '../../api'
import {defineStore} from 'pinia'
import useParameter from './parameter'

const baseUrl = '/api/parameters'

export const useParametersStore = defineStore('parameters', {
    actions: {
        dispose() {
            for (const parameter of this.parameters)
                parameter.dispose()
            this.$dispose()
        },
        async fetch() {
            const response = await api(this.url)
            for (const parameter of response['hydra:member'])
                this.parameters.push(useParameter(parameter, this))
        },
        async getByName(name) {
            const response = await api(`${baseUrl}?name=${name}`)
            for (const parameter of response['hydra:member'])
                this.parameter = useParameter(parameter, this)
        },
        removeParameter(removed) {
            this.parameters = this.parameters.filter(parameter => parameter['@id'] !== removed['@id'])
        }
    },
    getters: {
        fields: state => state.attributes.map(parameter => parameter.field),
        url() {
            return `/api/${this.$id}?pagination=false`
        }
    },
    state: () => ({
        parameter: {},
        parameters: []
    })
})
