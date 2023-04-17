import api from '../../api'
import {defineStore} from 'pinia'

export const useComponentListStore = defineStore('component', {
    actions: {
        async fetch() {
            const response = await api('/api/components/1', 'GET')
            this.component = await response
            console.log('this.component', this.component)
        },
        async update(data, id) {
            await api(`/api/components/${id}/logistics`, 'PATCH', data)
            this.fetch()
        },
        async updatePurchase(data, id) {
            await api(`/api/components/${id}/purchase`, 'PATCH', data)
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
