import {defineStore} from 'pinia'
import api from '../../api'

export const useCustomerProductStore = defineStore('customerProduct', {
    actions: {
        async fetch(criteria = '') {
            const response = await api(`api/customer-products${criteria}`, 'GET')
            this.customerProduct = response
            console.log('customerProduct', this.customerProduct)
        },
        async addCustomerProduct(payload) {
            console.log('payloadCustomerProduct', payload)
            for (let index = 0; index < payload.customer.length; index++) {
                const customerProduct = {
                    customer: payload.customer[index],
                    product: payload.product
                }
                await api('/api/customer-products', 'POST', customerProduct)
            }
        }
    },
    getters: {
        customerProductOption: state => state.customerProduct.map(item => {
            const newObject = {
                '@id': item['@id']
            }
            return newObject
        })
    },

    state: () => ({
        customerProduct: []
    })
})
