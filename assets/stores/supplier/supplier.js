import api from '../../api'
import {defineStore} from 'pinia'

export default function generateSupplier(suppliers) {
    return defineStore(`suppliers/${suppliers.id}`, {
        actions: {
            dispose() {
                this.$reset()
                this.$dispose()
            },
            
            async updateAccounting(data) {
                const response = await api(`/api/suppliers/${suppliers.id}/accounting`, 'PATCH', data)
                this.$state = {...response}
                console.log('updateQuality', response)
            }, 
            
            async updateAdmin(data) {
                const response = await api(`/api/suppliers/${suppliers.id}/admin`, 'PATCH', data)
                this.$state = {...response}
                console.log('update=', response)
            },
            async updateMain(data) {
                const response = await api(`/api/suppliers/${suppliers.id}/main`, 'PATCH', data)
                this.$state = {...response}
                console.log('update=', response)
            },
            async updateQuality(data) {
                const response = await api(`/api/suppliers/${suppliers.id}/quality`, 'PATCH', data)
                this.$state = {...response}
                console.log('updateQuality', response)
            },

        },
        getters: {
            getAddress: state => state.address.address,
            getAddress2: state => state.address.address2,
            getCity: state => state.address.city,
            getCountry: state => state.address.country,
            getEmail: state => state.address.email,
            getPhone: state => state.address.phoneNumber,
            getPostal: state => state.address.zipCode,
            incotermsValue: state => (state.incoterms ? state.incoterms['@id'] : null),
            vatMessageValue: state => (state.vatMessage ? state.vatMessage['@id'] : null)

        },
        state: () => ({...suppliers})
    })()
}
