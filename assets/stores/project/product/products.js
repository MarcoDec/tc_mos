import {defineStore} from 'pinia'
import api from '../../../api'
import generateProduct from './product'

export const useProductStore = defineStore('products', {
    actions: {
        async fetchOne(id = 1) {
            const response = await api(`/api/products/${id}`, 'GET')
            const item = generateProduct(response, this)
            this.product = item
            this.component = item
            this.isLoaded = true
        },
        async fetchAll(filter = '') {
            this.isLoading = true
            this.products = (await api(`/api/products${filter}`, 'GET'))['hydra:member']
            this.isLoading = false
            this.isLoaded = true
        },
        async fetchProductFamily() {
            const response = await api('/api/product-families', 'GET')
            this.productsFamily = response['hydra:member']
        },
        async fetch(criteria = '') {
            const response = await api(`/api/products${criteria}`, 'GET')
            this.productsItems = await this.updatePagination(response)
        },
        async updatePagination(response) {
            const responseData = await response['hydra:member']
            let paginationView = {}
            if (Object.prototype.hasOwnProperty.call(response, 'hydra:view')) {
                paginationView = response['hydra:view']
            } else {
                paginationView = responseData
            }
            if (Object.prototype.hasOwnProperty.call(paginationView, 'hydra:first')) {
                this.pagination = true
                this.firstPage = paginationView['hydra:first'] ? paginationView['hydra:first'].match(/page=(\d+)/)[1] : '1'
                this.lastPage = paginationView['hydra:last'] ? paginationView['hydra:last'].match(/page=(\d+)/)[1] : paginationView['@id'].match(/page=(\d+)/)[1]
                this.nextPage = paginationView['hydra:next'] ? paginationView['hydra:next'].match(/page=(\d+)/)[1] : paginationView['@id'].match(/page=(\d+)/)[1]
                this.currentPage = paginationView['@id'].match(/page=(\d+)/)[1]
                this.previousPage = paginationView['hydra:previous'] ? paginationView['hydra:previous'].match(/page=(\d+)/)[1] : paginationView['@id'].match(/page=(\d+)/)[1]
                return responseData
            }
            this.pagination = false
            return responseData
        },
        async remove(id){
            await api(`/api/products/${id}`, 'DELETE')
            this.productsItems = this.productsItems.filter(productItem => Number(productItem['@id'].match(/\d+/)[0]) !== id)
        },
        async addProduct(payload) {
            const response = await api('/api/products', 'POST', payload)
            this.currentProduct = response['@id']
        },
        reset() {
            this.product = {}
            this.products = []
            this.isLoaded = false
            this.isLoading = false
            this.productsFamily = []
            this.component = {}
            this.productsItems = []
            this.currentProduct = ''
        }
    },
    getters: {
        productItem: state => state.productsItems.map(item => {
            const newObject = {
                '@id': item['@id'],
                type: item['@type'],
                code: item.code,
                index: item.index,
                stateBlocker: item.embBlocker.state,
                state: item.embState.state,
                endOfLife: item.endOfLife,
                family: item.family,
                filePath: item.filePath,
                id: item.id,
                kind: item.kind,
                name: item.name,
                companies: [], //item.companies.map(company => ({id: Number(company['@id'].split('/').pop())})),
                customers: item.customers
            }
            return newObject
        })
    },
    state: () => ({
        isLoaded: false,
        isLoading: false,
        product: {},
        products: [],
        productsFamily: [],
        component: {},
        productsItems: [],
        currentProduct: ''
    })
})
