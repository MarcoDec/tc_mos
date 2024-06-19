import {defineStore} from 'pinia'

export default function generateCountry(country, root) {
    return defineStore(`countries/${country.code}`, {
        actions: {
            dispose() {
                this.$reset()
                this.$dispose()
            }
        },
        getters: {
            option: state => ({text: state.name, value: state.code}),
            phoneLabel: state => `${state.name}`
        },
        state: () => ({root, ...country})
    })()
}
