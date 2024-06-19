import api from '../../../api'
import {defineStore} from 'pinia'

export const usePurchaseOrderStore = defineStore('purchaseOrder', {
    actions: {
        async fetch() {
            const response = await api('/api/purchase-orders', 'GET')
            this.purchaseOrders = response['hydra:member']
        },
        async fetchById(id) {
            const response = await api(`/api/purchase-orders/${id}`, 'GET')
            this.purchaseOrder = response
        }
    },
    getters: {
        order: state => state.purchaseOrder.order
    },
    state: () => ({
        purchaseOrders: [],
        purchaseOrder: []
    })
})
