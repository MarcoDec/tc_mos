import api from '../../api'
import {defineStore} from 'pinia'

export const useCustomerAddressStore = defineStore('customerAddress', {
    actions: {
        async fetchDeliveryAddress(filter) {
            const response = await api(`/api/delivery-addresses${filter}`, 'GET')
            this.deliveryAddresses = response['hydra:member']
        },
        async fetchBillingAddress(filter) {
            const response = await api(`/api/billing-addresses${filter}`, 'GET')
            this.billingAddresses = response['hydra:member']
        }
    },
    getters: {
        billingAddressesOption: state => state.billingAddresses.map(billingAddress => {
            const opt = {
                customer: billingAddress.customer['@id'],
                text: billingAddress.address.address,
                value: billingAddress['@id']
            }
            return opt
        }),
        deliveryAddressesOption: state => state.deliveryAddresses.map(deliveryAddress => {
            const opt = {
                customer: deliveryAddress.customer['@id'],
                text: deliveryAddress.address.address,
                value: deliveryAddress['@id']
            }
            return opt
        })
    },
    state: () => ({
        billingAddresses: [],
        deliveryAddresses: []
    })
})
