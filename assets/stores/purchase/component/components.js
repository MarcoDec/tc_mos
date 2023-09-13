import api from '../../../api'
import {defineStore} from 'pinia'

export const useComponentListStore = defineStore('component', {
    actions: {
        async fetchOne(id = 1) {
            this.isLoading = true
            this.component = await api(`/api/components/${id}`, 'GET')
            this.isLoading = false
            this.isLoaded = true
        },
        async fetchAll(filter = '') {
            this.isLoading = true
            this.components = (await api(`/api/components${filter}`, 'GET'))['hydra:member']
            this.isLoading = false
            this.isLoaded = true
        },
        async update(data, id) {
            await api(`/api/components/${id}/logistics`, 'PATCH', data)
            await this.fetchOne(id)
        },
        async updateAdmin(data, id) {
            await api(`/api/components/${id}/admin`, 'PATCH', data)
        },
        async updateMain(data, id) {
            await api(`/api/components/${id}/main`, 'PATCH', data)
        },
        async updatePrice(data, id) {
            await api(`/api/components/${id}/price`, 'PATCH', data)
            await this.fetchOne(id)
        },
        async updatePurchase(data, id) {
            await api(`/api/components/${id}/purchase`, 'PATCH', data)
            await this.fetchOne(id)
        },
        async updateQuality(data, id) {
            await api(`/api/components/${id}/quality`, 'PATCH', data)
            await this.fetchOne(id)
        }
    },
    getters: {
        getWeight: state => state.component.weight.value,
        getWeightCode: state => state.component.weight.code
    },
    state: () => ({
        component: {},
        components: [],
        isLoaded: false,
        isLoading: false
    })
})
