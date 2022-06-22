import {defineStore} from 'pinia'

export default function generateField(form, field) {
    return defineStore(`${form}/fields/${field.name}`, {
        actions: {
            dispose() {
                this.$reset()
                this.$dispose()
            }
        },
        getters: {
            safeType: state => state.type ?? 'text'
        },
        state: () => field
    })
}
