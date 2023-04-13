import {defineStore} from 'pinia'

export default function generateSupplier(suppliers) {
    return defineStore(`suppliers/${suppliers.id}`, {
        actions: {
            dispose() {
                this.$reset()
                this.$dispose()
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
        },
        state: () => ({...suppliers})
    })()
}
