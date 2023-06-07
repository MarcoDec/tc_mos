import {defineStore} from 'pinia'

export default function generateSupplierCompany(supplierCompany) {
    return defineStore(`supplier-companies/${supplierCompany.id}`, {
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
            // incotermsValue: state => (state.incoterms ? state.incoterms['@id'] : null),
            // vatMessageValue: state => (state.vatMessage ? state.vatMessage['@id'] : null)

        },
        state: () => ({...supplierCompany})
    })()
}
