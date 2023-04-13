import {defineStore} from 'pinia'

export default function generateCustomer(customer) {
    return defineStore(`customers/${customer.id}`, {
        actions: {
            dispose() {
                this.$reset()
                this.$dispose()
            }
        },
        getters: {
            getAddress: state => state.address.address,
            getCity: state => state.address.city,
            getCountry: state => state.address.country,
            getEmail: state => state.address.email,
            getPhone: state => state.address.phoneNumber,
            getPostal: state => state.address.zipCode
        },
        state: () => ({...customer})
    })()
}
