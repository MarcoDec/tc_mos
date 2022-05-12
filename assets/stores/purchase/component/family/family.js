import {defineStore} from 'pinia'

export default function generateFamily(family, root) {
    const name = `component-family/${family.id}`
    return defineStore(name, {
        getters: {
            children: state => state.root.findByParent(state['@id']),
            fullName(state) {
                return this.parentStore ? `${this.parentStore.fullName}\\${state.name}` : state.name
            },
            hasChildren() {
                return this.children.length > 0
            },
            isRoot: state => !state.parent,
            parentStore: state => state.root.find(state.parent)
        },
        state: () => ({root, ...family})
    })()
}
