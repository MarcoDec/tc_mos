import api from '../../api'
import {defineStore} from 'pinia'

export const useComponentListStore = defineStore('component', {
    actions: {
        async fetchOne(id = 1) {
            const response = await api(`/api/components/${id}`, 'GET')
            this.component = await response
        },
        async update(data, id) {
            await api(`/api/components/${id}/logistics`, 'PATCH', data)
            this.fetchOne(id)
        },
        async updateAdmin(data, id) {
            await api(`/api/components/${id}/admin`, 'PATCH', data)
        },
        async updateMain(data, id) {
            await api(`/api/components/${id}/main`, 'PATCH', data)
        },
        async updatePrice(data, id) {
            await api(`/api/components/${id}/price`, 'PATCH', data)
            this.fetchOne(id)
        },
        async updatePurchase(data, id) {
            await api(`/api/components/${id}/purchase`, 'PATCH', data)
            this.fetchOne(id)
        },
        async updateQuality(data, id) {
            await api(`/api/components/${id}/quality`, 'PATCH', data)
            this.fetchOne(id)
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
