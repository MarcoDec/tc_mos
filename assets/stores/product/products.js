import api from '../../api'
import {defineStore} from 'pinia'
import generateProduct from './product'

export const useProductStore = defineStore('products', {
    actions: {
        async fetchOne(id = 1) {
            const response = await api(`/api/products/${id}`, 'GET')
            const item = generateProduct(response, this)
            this.product = item
            this.isLoaded = true
        },
        async fetchProductFamily() {
            const response = await api('/api/product-families', 'GET')
            this.productsFamily = response['hydra:member']
        }
    },
    getters: {},
    state: () => ({
        isLoaded: false,
        product: {},
        products: {},
        productsFamily: []
    })
})
