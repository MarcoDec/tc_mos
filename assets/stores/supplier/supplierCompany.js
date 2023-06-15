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
        },
        state: () => ({...supplierCompany})
    })()
}
