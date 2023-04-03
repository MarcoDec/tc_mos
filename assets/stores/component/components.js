import api from '../../api'
import {defineStore} from 'pinia'

export const useComponentListStore = defineStore('component', {
    actions: {
        async fetch() {
            const response = await api('/api/components/1', 'GET')
            console.log('response ***===', response)
            this.component = await response
        },
        async update(data, id) {
            const response = await api(`/api/components/${id}/admin`, 'PATCH', data)
            console.log('api response update', response)
            this.fetch()
        }
    },
    getters: {
        getWeight: state => state.component.weight.value,
        getWeightCode: state => state.component.weight.code
    },
    state: () => ({
        component: {}
    })
})
