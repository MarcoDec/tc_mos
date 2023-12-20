import api from '../../api'
import {defineStore} from 'pinia'

export const useCustomerAddressStore = defineStore('customerAddress', {
    actions: {
        async fetchDeliveryAddress() {
            const response = await api('/api/delivery-addresses', 'GET')
            this.deliveryAddresses = response['hydra:member']
        }
    },
    getters: {
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
        deliveryAddresses: []
    })
})
