import api from '../../api'
import {defineStore} from 'pinia'

export default function generateSupplier(supplier) {
    return defineStore(`suppliers/${supplier.id}`, {
        actions: {
            dispose() {
                this.$reset()
                this.$dispose()
            },

            async updateAccounting(data) {
                const response = await api(`/api/suppliers/${supplier.id}/accounting`, 'PATCH', data)
                this.$state = {...response}
            },

            async updateAdmin(data) {
                const response = await api(`/api/suppliers/${supplier.id}/admin`, 'PATCH', data)
                this.$state = {...response}
            },
            async updateLog(data) {
                const response = await api(`/api/suppliers/${supplier.id}/purchase-logistics`, 'PATCH', data)
                this.$state = {...response}
            },
            async updateMain(data) {
                const response = await api(`/api/suppliers/${supplier.id}/main`, 'PATCH', data)
                this.$state = {...response}
            },
            async updateQuality(data) {
                const response = await api(`/api/suppliers/${supplier.id}/quality`, 'PATCH', data)
                this.$state = {...response}
            }
        },
        getters: {
            getAddress: state => state.address.address,
            getAddress2: state => state.address.address2,
            getCity: state => state.address.city,
            getCountry: state => state.address.country,
            getEmail: state => state.address.email,
            getPhone: state => state.address.phoneNumber,
            getPostal: state => state.address.zipCode
            // incotermsValue: state => (state.incoterms ? state.incoterms['@id'] : null),
            // vatMessageValue: state => (state.vatMessage ? state.vatMessage['@id'] : null)
        },
        state: () => ({...supplier})
    })()
}
