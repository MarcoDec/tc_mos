import api from '../../api'
import {defineStore} from 'pinia'

export default function generateSocieties(societies) {
    return defineStore(`societies/${societies.id}`, {
        actions: {
            dispose() {
                this.$reset()
                this.$dispose()
            },
            async update(data) {
                const response = await api(`/api/societies/${societies.id}`, 'PATCH', data)
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
            getPostal: state => state.address.zipCode,
            // getUsername: state => state.accountingPortal.username,
            // getPassword: state => state.accountingPortal.password,
            // getUrl: state => state.accountingPortal.url,
            incotermsValue: state => (state.incoterms ? state.incoterms['@id'] : null),
            vatMessageValue: state => (state.vatMessage ? state.vatMessage['@id'] : null)
        },
        state: () => ({...societies})
    })()
}
