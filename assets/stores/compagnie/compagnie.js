import {defineStore} from 'pinia'

export default function generateCompagnie(compagnie, root) {
    return defineStore(`compagnies/${compagnie.id}`, {
        actions: {
            dispose() {
                this.$reset()
                this.$dispose()
            }
        },
        getters: {
            option: state => ({text: state.name, value: state.name}),
            phoneLabel: state => `${state.name}`
        },
        state: () => ({root, ...compagnie})
    })()
}
