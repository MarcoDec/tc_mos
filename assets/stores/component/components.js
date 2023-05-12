import api from '../../api'
import {defineStore} from 'pinia'

export const useComponentListStore = defineStore('component', {
    actions: {
        async fetch() {
            const response = await api('/api/components/1', 'GET')
            this.component = await response
        },
        async update(data, id) {
            await api(`/api/components/${id}/logistics`, 'PATCH', data)
            this.fetch()
        },
        async updateAdmin(data, id) {
            await api(`/api/components/${id}/admin`, 'PATCH', data)
            this.fetch()
        },
        async updateMain(data, id) {
            await api(`/api/components/${id}/main`, 'PATCH', data)
            this.fetch()
        },
        async updatePrice(data, id) {
            await api(`/api/components/${id}/price`, 'PATCH', data)
            this.fetch()
        },
        async updatePurchase(data, id) {
            await api(`/api/components/${id}/purchase`, 'PATCH', data)
            this.fetch()
        },
        async updateQuality(data, id) {
            await api(`/api/components/${id}/quality`, 'PATCH', data)
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
