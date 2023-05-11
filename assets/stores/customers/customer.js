import api from '../../api'
import {defineStore} from 'pinia'

export default function generateCustomer(customers) {
    return defineStore(`customers/${customers.id}`, {
        actions: {
            dispose() {
                this.$reset()
                this.$dispose()
            },

            async update(data) {
                const response = await api(`/api/customers/${customers.id}/logistics`, 'PATCH', data)
                this.$state = {...response}
            },
            async updateAccounting(data) {
                const response = await api(`/api/customers/${customers.id}/accounting`, 'PATCH', data)
                this.$state = {...response}
            },
            async updateMain(data) {
                const response = await api(`/api/customers/${customers.id}/main`, 'PATCH', data)
                this.$state = {...response}
            }
        },
        getters: {
            getAddress: state => state.address.address,
            getAddress2: state => state.address.address2,
            getCity: state => state.address.city,
            getCountry: state => state.address.country,
            getEmail: state => state.address.email,
            getPassword: state => state.accountingPortal.password,
            getPhone: state => state.address.phoneNumber,
            getPostal: state => state.address.zipCode,
            getUrl: state => state.accountingPortal.url,
            getUsername: state => state.accountingPortal.username
            // vatMsg: state => (state.vatMessage ? state.vatMessage['@id'] : null),
            // incotermsValue: state => (state.incoterms ? state.incoterms['@id'] : null)
        },
        state: () => ({...customers})
    })()
}
