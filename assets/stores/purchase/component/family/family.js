import {defineStore} from 'pinia'

export default function generateFamily(family, root) {
    const name = `component-family/${family.id}`
    return defineStore(name, {
        getters: {
            fullName(state) {
                return this.parentStore ? `${this.parentStore.fullName}\\${state.name}` : state.name
            },
            parentStore: state => state.root.find(state.parent)
        },
        state: () => ({root, ...family})
    })()
}
