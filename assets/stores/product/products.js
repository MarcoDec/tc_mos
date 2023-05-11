import api from '../../api'
import {defineStore} from 'pinia'
import generateProduct from './product'

export const useProductStore = defineStore('products', {
    actions: {
        async fetch() {
            const response = await api('/api/products/1', 'GET')
            console.log('res', response)
            const item = generateProduct(response, this)
            this.products = item
        },
        async fetchProductFamily() {
            const response = await api('/api/product-families', 'GET')
            this.productsFamily = response['hydra:member']
            console.log('response', response['hydra:member'])
        }
    },
    getters: {},
    state: () => ({
        products: {},
        productsFamily: []
    })
})
