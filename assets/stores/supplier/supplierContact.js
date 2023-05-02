import api from '../../api'
import {defineStore} from 'pinia'

export default function generateSupplierContact(items) {
    return defineStore(`supplier-contacts/${items.id}`, {
        actions: {
            dispose() {
                this.$reset()
                this.$dispose()
            },
            async update(data) {
                const response = await api(`/api/supplier-contacts/${items.id}`, 'PATCH', data)
                this.$state = {...response}
                console.log('update-contacts', response)
            }, 
        },
        getters: {
            getAddress: state => state.address.address,
            getAddress2: state => state.address.address2,
            getCity: state => state.address.city,
            getCountry: state => state.address.country,
            getEmail: state => state.address.email,
            getPhone: state => state.address.phoneNumber,
            getPostal: state => state.address.zipCode
           
        },
        state: () => ({...items})
    })()
}
